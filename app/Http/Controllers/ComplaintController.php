<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\Branch;
use App\Models\Employer;
use App\Models\PaymentMethod;
use App\Models\Department;
use App\Models\ComplaintComment;
use App\Services\ActivityLogService;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // OPTIMIZED: Single query for status counts using DB::raw for better performance
        $statusCountsQuery = Complaint::query()
            ->forUser($user)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "in_progress" THEN 1 ELSE 0 END) as in_progress,
                SUM(CASE WHEN status = "partial_closed" THEN 1 ELSE 0 END) as partial_closed,
                SUM(CASE WHEN status = "resolved" THEN 1 ELSE 0 END) as resolved,
                SUM(CASE WHEN status = "escalated" THEN 1 ELSE 0 END) as escalated,
                SUM(CASE WHEN status = "assigned" THEN 1 ELSE 0 END) as assigned,
                SUM(CASE WHEN status = "closed" THEN 1 ELSE 0 END) as closed
            ')
            ->first();

        $statusCounts = [
            'pending' => $statusCountsQuery->pending ?? 0,
            'in_progress' => $statusCountsQuery->in_progress ?? 0,
            'partial_closed' => $statusCountsQuery->partial_closed ?? 0,
            'resolved' => $statusCountsQuery->resolved ?? 0,
            'escalated' => $statusCountsQuery->escalated ?? 0,
            'assigned' => $statusCountsQuery->assigned ?? 0,
            'closed' => $statusCountsQuery->closed ?? 0,
        ];

        // OPTIMIZED: Build filtered query using scopes for cleaner code and better performance
        $complaints = Complaint::with(['capturedBy', 'assignedTo'])
            ->forUser($user)
            ->byStatus($request->status)
            ->byPriority($request->priority)
            ->byBranch($request->branch_id)
            ->dateRange($request->start_date, $request->end_date)
            ->search($request->search)
            ->latest()
            ->paginate(15)
            ->appends($request->query());

        $branches = Branch::orderBy('name')->get();

        return view('complaints.index', compact('complaints', 'statusCounts', 'branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $employers = Employer::active()->orderBy('name')->get();
        $paymentMethods = PaymentMethod::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        return view('complaints.create', compact('branches', 'employers', 'paymentMethods', 'departments'));
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
                'phone_number' => ['required', 'string', 'size:12', 'regex:/^263\d{9}$/'],
                'location' => ['required', 'string', 'max:255'],
                'visited_branch' => ['nullable', 'string', 'max:255'],
                'branch_id' => ['required', 'exists:branches,id'],
                'employer_id' => ['required', 'exists:employers,id'],
                'payment_method_id' => ['required', 'exists:payment_methods,id'],
                'department_id' => ['required', 'exists:departments,id'],
                'complaint_text' => ['required', 'string', 'max:10000'],
                'priority' => ['required', 'in:low,medium,high,urgent'],
            ], [
                'phone_number.size' => 'Phone number must be exactly 12 digits in international format (e.g., 263776905912)',
                'phone_number.regex' => 'Phone number must start with 263 followed by 9 digits (e.g., 263776905912)',
            ]);

            // Sanitize text inputs
            $validated['full_name'] = strip_tags($validated['full_name']);
            $validated['location'] = strip_tags($validated['location']);
            if (!empty($validated['visited_branch'])) {
                $validated['visited_branch'] = strip_tags($validated['visited_branch']);
            }
            $validated['complaint_text'] = strip_tags($validated['complaint_text']);

            $validated['captured_by'] = Auth::id();
            $validated['assigned_to'] = Auth::id(); // Auto-assign to creator
            $validated['status'] = 'assigned'; // Set to assigned since we have an assignee

            // Sync visited_branch from branch
            $branch = Branch::find($validated['branch_id']);
            $validated['visited_branch'] = $branch?->name;

            $complaint = Complaint::create($validated);

            // Log activity
            ActivityLogService::logTicketCreated($complaint);

            // Send notifications asynchronously to avoid blocking the response
            // This dispatches to queue so ticket creation is fast
            if ($complaint->assigned_to) {
                $assignedUser = User::find($complaint->assigned_to);
                dispatch(function () use ($complaint, $assignedUser) {
                    NotificationService::notifyTicketAssigned($complaint, $assignedUser);
                })->afterResponse();
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
        $branches = Branch::orderBy('name')->get();
        $employers = Employer::active()->orderBy('name')->get();
        $paymentMethods = PaymentMethod::active()->orderBy('name')->get();
        $departments = Department::active()->orderBy('name')->get();
        return view('complaints.edit', compact('complaint', 'agents', 'branches', 'employers', 'paymentMethods', 'departments'));
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
                'status' => ['required', 'in:pending,assigned,in_progress,resolved,closed,escalated,partial_closed'],
                'priority' => ['required', 'in:low,medium,high,urgent'],
                'assigned_to' => ['nullable', 'exists:users,id'],
                'branch_id' => ['required', 'exists:branches,id'],
                'employer_id' => ['required', 'exists:employers,id'],
                'payment_method_id' => ['required', 'exists:payment_methods,id'],
                'resolution_notes' => ['nullable', 'string', 'max:10000'],
                'comment' => ['nullable', 'string', 'max:5000'],
                // Partial closed fields
                'pending_department' => ['nullable', 'required_if:status,partial_closed', 'in:Billing,Claims,IT,General Support'],
                'completed_department' => ['nullable', 'in:Billing,Claims,IT,General Support'],
                'partial_close_notes' => ['nullable', 'string', 'max:5000'],
            ]);

            // Sanitize text inputs
            if (!empty($validated['resolution_notes'])) {
                $validated['resolution_notes'] = strip_tags($validated['resolution_notes']);
            }
            if (!empty($validated['partial_close_notes'])) {
                $validated['partial_close_notes'] = strip_tags($validated['partial_close_notes']);
            }

            // Sync visited_branch from branch
            $branch = Branch::find($validated['branch_id']);
            $validated['visited_branch'] = $branch?->name;

            $oldStatus = $complaint->status;
            $oldAssignedTo = $complaint->assigned_to;
            $changes = [];

            // Handle partial closed status
            if ($validated['status'] === 'partial_closed') {
                $validated['partial_closed_at'] = now();
                // Set completed department to current department if not specified
                if (empty($validated['completed_department'])) {
                    $validated['completed_department'] = $complaint->department;
                }
            } elseif ($oldStatus === 'partial_closed' && $validated['status'] !== 'partial_closed') {
                // If moving from partial_closed to another status, clear partial close fields
                if (in_array($validated['status'], ['resolved', 'closed'])) {
                    // Keep the notes for record but clear pending department
                    $validated['pending_department'] = null;
                }
            }

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

                // Build status change message
                $statusMessage = 'status changed to ' . ucwords(str_replace('_', ' ', $validated['status']));
                if ($validated['status'] === 'partial_closed' && !empty($validated['pending_department'])) {
                    $statusMessage .= ' (awaiting ' . $validated['pending_department'] . ')';
                }
                $changes[] = $statusMessage;

                // Send WhatsApp notification to customer about status change
                NotificationService::sendWhatsAppStatusUpdate($complaint, $oldStatus, $validated['status']);
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
