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
        $query = Complaint::with(['capturedBy', 'assignedTo'])->latest();

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

        // Filter tickets based on role
        $user = Auth::user();
        if ($user->role === 'support_agent') {
            // Support agents see only assigned tickets
            $query->where('assigned_to', $user->id);
        } elseif ($user->role === 'user') {
            // Regular users see only their created tickets
            $query->where('captured_by', $user->id);
        }
        // Admins and managers see all tickets (no filter)

        $complaints = $query->paginate(15);

        return view('complaints.index', compact('complaints'));
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
                'policy_number' => ['required', 'string', 'max:255'],
                'full_name' => ['required', 'string', 'max:255'],
                'phone_number' => ['required', 'string', 'max:255'],
                'location' => ['required', 'string', 'max:255'],
                'visited_branch' => ['required', 'string', 'max:255'],
                'department' => ['required', 'in:Billing,Claims,IT,General Support'],
                'complaint_text' => ['required', 'string'],
                'priority' => ['required', 'in:low,medium,high,urgent'],
            ]);

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
        $agents = User::whereIn('role', ['support_agent', 'manager', 'super_admin'])->get();
        return view('complaints.edit', compact('complaint', 'agents'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Complaint $complaint)
    {
        try {
            $validated = $request->validate([
                'status' => ['required', 'in:pending,assigned,in_progress,resolved,closed,escalated'],
                'priority' => ['required', 'in:low,medium,high,urgent'],
                'assigned_to' => ['nullable', 'exists:users,id'],
                'resolution_notes' => ['nullable', 'string'],
                'comment' => ['nullable', 'string'],
            ]);

            $oldStatus = $complaint->status;
            $oldAssignedTo = $complaint->assigned_to;
            $changes = [];

            $complaint->update(collect($validated)->except('comment')->toArray());

            // Add comment if provided
            if ($request->filled('comment')) {
                ComplaintComment::create([
                    'complaint_id' => $complaint->id,
                    'user_id' => Auth::id(),
                    'comment' => $request->comment,
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
            if ($request->filled('assigned_to') && $oldAssignedTo !== $request->assigned_to) {
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
     * Remove the specified resource from storage.
     */
    public function destroy(Complaint $complaint)
    {
        $ticketNumber = $complaint->ticket_number;
        $complaint->delete();

        ActivityLogService::log(
            'ticket_deleted',
            "Deleted ticket {$ticketNumber}",
            null,
            ['ticket_number' => $ticketNumber]
        );

        return redirect()->route('complaints.index')
            ->with('success', 'Complaint deleted successfully!');
    }

    /**
     * Assign ticket to user
     */
    public function assign(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'assigned_to' => ['required', 'exists:users,id'],
        ]);

        $complaint->update($validated);
        $complaint->update(['status' => 'assigned']);

        $assignedUser = User::find($request->assigned_to);
        ActivityLogService::logTicketAssigned($complaint, $assignedUser);

        return back()->with('success', 'Ticket assigned successfully!');
    }
}
