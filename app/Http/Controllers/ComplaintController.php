<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\ComplaintComment;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Base query for the user's accessible complaints
        $user = Auth::user();
        $baseQuery = Complaint::query();

        if ($user->role === 'user') {
            // Regular users see only their created tickets
            $baseQuery->where('captured_by', $user->id);
        }
        // Support agents, managers, and admins see all tickets

        // Get status counts BEFORE applying filters (for accurate stat cards)
        $statusCounts = [
            'pending' => (clone $baseQuery)->where('status', 'pending')->count(),
            'in_progress' => (clone $baseQuery)->where('status', 'in_progress')->count(),
            'resolved' => (clone $baseQuery)->where('status', 'resolved')->count(),
            'escalated' => (clone $baseQuery)->where('status', 'escalated')->count(),
            'assigned' => (clone $baseQuery)->where('status', 'assigned')->count(),
            'closed' => (clone $baseQuery)->where('status', 'closed')->count(),
        ];

        // Now build the filtered query
        $query = Complaint::with(['capturedBy', 'assignedTo'])->latest();

        // Apply role-based filtering
        if ($user->role === 'user') {
            $query->where('captured_by', $user->id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('policy_number', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $complaints = $query->paginate(15);

        return view('complaints.index', compact('complaints', 'statusCounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('complaints.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'policy_number' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z0-9\-_\/]+$/'],
                'full_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:20', 'regex:/^[\d\s\+\-\(\)]+$/'],
                'location' => ['required', 'string', 'max:255'],
                'visited_branch' => ['required', 'string', 'max:255'],
                'department' => ['required', 'in:Billing,Claims,IT,General Support'],
                'complaint_text' => ['required', 'string', 'max:10000'],
                'priority' => ['required', 'in:low,medium,high,urgent'],
            ]);

            // Sanitize text inputs
            $validated['full_name'] = strip_tags($validated['full_name']);
            $validated['location'] = strip_tags($validated['location']);
            $validated['visited_branch'] = strip_tags($validated['visited_branch']);
            $validated['complaint_text'] = strip_tags($validated['complaint_text']);

            $validated['captured_by'] = Auth::id();
            $validated['assigned_to'] = Auth::id(); // Auto-assign to creator
            $validated['status'] = 'assigned'; // Set to assigned since we have an assignee

            $complaint = Complaint::create($validated);

            // Log activity
            ActivityLogService::logTicketCreated($complaint);

            // Send notification to assigned user
            if ($complaint->assigned_to) {
                $assignedUser = User::find($complaint->assigned_to);
                NotificationService::notifyTicketAssigned($complaint, $assignedUser);
            }

            return redirect()->route('complaints.show', $complaint)
                ->with('success', '✅ Ticket created successfully! Ticket #' . $complaint->ticket_number . ' has been assigned to you.');
        } catch (\Exception $e) {
            Log::error('Error creating ticket: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to create ticket. Please try again or contact support.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Complaint $complaint)
    {
        // Authorization: Check if user can view this complaint
        $user = Auth::user();
        // Regular users can only see their own tickets
        if ($user->role === 'user' && $complaint->captured_by !== $user->id) {
            abort(403, 'You can only view tickets you created.');
        }
        // Support agents, managers, and admins can view all tickets
        // This allows agents to take over tickets when colleagues are unavailable

        $complaint->load(['capturedBy', 'assignedTo', 'comments.user']);

        // Load activity logs for this ticket with pagination
        $activities = \App\Models\ActivityLog::where('model_type', 'App\\Models\\Complaint')
            ->where('model_id', $complaint->id)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('complaints.show', compact('complaint', 'activities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Complaint $complaint)
    {
        // Authorization: Check if user can edit this complaint
        $user = Auth::user();
        // Regular users cannot edit tickets
        if ($user->role === 'user') {
            abort(403, 'You do not have permission to edit tickets.');
        }
        // Support agents, managers, and admins can edit all tickets
        // This allows agents to take over tickets when colleagues are unavailable

        $agents = User::whereIn('role', ['support_agent', 'manager', 'super_admin'])->get();
        return view('complaints.edit', compact('complaint', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint)
    {
        // Authorization check
        $user = Auth::user();
        // Regular users cannot update tickets
        if ($user->role === 'user') {
            abort(403, 'You do not have permission to update tickets.');
        }
        // Support agents, managers, and admins can update all tickets
        // This allows agents to take over tickets when colleagues are unavailable

        try {
            $validated = $request->validate([
                'status' => ['required', 'in:pending,assigned,in_progress,resolved,closed,escalated'],
                'priority' => ['required', 'in:low,medium,high,urgent'],
                'assigned_to' => ['nullable', 'exists:users,id'],
                'resolution_notes' => ['nullable', 'string', 'max:10000'],
                'comment' => ['nullable', 'string', 'max:5000'],
            ]);

            // Sanitize text inputs
            if (!empty($validated['resolution_notes'])) {
                $validated['resolution_notes'] = strip_tags($validated['resolution_notes']);
            }

            $oldStatus = $complaint->status;
            $oldAssignedTo = $complaint->assigned_to;
            $changes = [];

            $complaint->update(collect($validated)->except('comment')->toArray());

            // Add comment if provided
            if ($request->filled('comment')) {
                ComplaintComment::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => Auth::id(),
                    'comment' => strip_tags($request->comment),
                    'is_internal' => true,
                ]);

                ActivityLogService::log(
                    'ticket_commented',
                    "Added comment to ticket {$complaint->ticket_number}",
                    $complaint
                );
                $changes[] = 'comment added';
            }

            // Log status change
            if ($oldStatus !== $validated['status']) {
                ActivityLogService::logTicketStatusUpdate($complaint, $oldStatus, $validated['status']);
                $changes[] = 'status changed to ' . ucwords(str_replace('_', ' ', $validated['status']));
            }

            // Log assignment change
            if ($request->filled('assigned_to') && $oldAssignedTo !== (int) $request->assigned_to) {
                $assignedUser = User::find($request->assigned_to);
                ActivityLogService::logTicketAssigned($complaint, $assignedUser);

                // Send notification to newly assigned user
                NotificationService::notifyTicketAssigned($complaint, $assignedUser);

                $changes[] = 'assigned to ' . $assignedUser->name;
            }

            // Update resolved/closed timestamps
            if ($validated['status'] === 'resolved' && !$complaint->resolved_at) {
                $complaint->resolved_at = now();
                $complaint->save();
            }

            if ($validated['status'] === 'closed' && !$complaint->closed_at) {
                $complaint->closed_at = now();
                $complaint->save();
            }

            $message = '✅ Ticket updated successfully!';
            if (!empty($changes)) {
                $message .= ' Changes: ' . implode(', ', $changes) . '.';
            }

            return redirect()->route('complaints.show', $complaint)
                ->with('success', $message);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors())
                ->with('error', '❌ Please check the form for errors.');
        } catch (\Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', '❌ Failed to update ticket. Please try again or contact support.');
        }
    }

    /**
     * Assign ticket to user (supports takeover by any agent)
     */
    public function assign(Request $request, Complaint $complaint)
    {
        // Allow support agents, managers, and admins to assign/takeover tickets
        $user = Auth::user();
        if (!in_array($user->role, ['super_admin', 'manager', 'support_agent'])) {
            abort(403, 'Unauthorized to assign tickets.');
        }

        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $oldAssignedTo = $complaint->assignedTo;
        $complaint->update($validated);

        // Only change status to 'assigned' if it was pending
        if ($complaint->status === 'pending') {
            $complaint->update(['status' => 'assigned']);
        }

        $assignedUser = User::find($request->assigned_to);
        ActivityLogService::logTicketAssigned($complaint, $assignedUser);

        // Log takeover activity if it was reassigned from another user
        if ($oldAssignedTo && $oldAssignedTo->id !== $assignedUser->id) {
            ActivityLogService::log(
                'ticket_takeover',
                "Ticket {$complaint->ticket_number} taken over from {$oldAssignedTo->name} by {$user->name}, assigned to {$assignedUser->name}",
                $complaint,
                [
                    'previous_assignee' => $oldAssignedTo->name,
                    'new_assignee' => $assignedUser->name,
                    'taken_over_by' => $user->name,
                ]
            );
        }

        // Send notification to newly assigned user
        if ($assignedUser->id !== $user->id) {
            NotificationService::notifyTicketAssigned($complaint, $assignedUser);
        }

        return back()->with('success', 'Ticket assigned successfully to ' . $assignedUser->name . '!');
    }
}
