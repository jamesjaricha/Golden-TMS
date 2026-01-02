<?php

namespace App\Http\Controllers;

use App\Models\TicketReminder;
use App\Models\Complaint;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketReminderController extends Controller
{
    /**
     * Authorise user access to ticket reminders
     * Regular users can only access their own tickets
     */
    protected function authoriseTicketAccess(Complaint $complaint): void
    {
        $user = Auth::user();

        // Regular users can only access tickets they created
        if ($user->role === 'user' && $complaint->captured_by !== $user->id) {
            abort(403, 'You do not have permission to manage reminders for this ticket.');
        }
    }

    /**
     * Verify reminder belongs to the complaint (prevents route manipulation)
     */
    protected function verifyReminderOwnership(Complaint $complaint, TicketReminder $reminder): void
    {
        if ($reminder->complaint_id !== $complaint->id) {
            abort(404, 'Reminder not found for this ticket.');
        }
    }

    /**
     * Store a new reminder for a ticket
     */
    public function store(Request $request, Complaint $complaint)
    {
        // SECURITY: Authorise access to this ticket
        $this->authoriseTicketAccess($complaint);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_description' => 'required|string|max:1000',
            'reminder_datetime' => 'required|date|after:now',
            'priority' => 'required|in:low,medium,high',
            'notes' => 'nullable|string|max:2000',
        ]);

        // Sanitise text inputs
        $validated['task_description'] = strip_tags($validated['task_description']);
        if (!empty($validated['notes'])) {
            $validated['notes'] = strip_tags($validated['notes']);
        }

        $validated['complaint_id'] = $complaint->id;
        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending';

        $reminder = TicketReminder::create($validated);

        // Send immediate notification when task is assigned
        $this->sendTaskAssignedNotification($reminder);

        return redirect()
            ->route('complaints.show', $complaint->ticket_number)
            ->with('success', 'Task reminder created successfully.');
    }

    /**
     * Send notification when a task is assigned (immediate, not waiting for due time)
     */
    protected function sendTaskAssignedNotification(TicketReminder $reminder)
    {
        $user = $reminder->user;
        $complaint = $reminder->complaint;
        $createdBy = User::find($reminder->created_by);

        // Create in-app notification
        NotificationService::create(
            $user->id,
            'task_assigned',
            '📋 New Task Assigned',
            "You have been assigned a task: {$reminder->task_description}",
            route('complaints.show', $complaint->ticket_number),
            [
                'reminder_id' => $reminder->id,
                'ticket_id' => $complaint->id,
                'ticket_number' => $complaint->ticket_number,
                'priority' => $reminder->priority,
                'due_at' => $reminder->reminder_datetime->toDateTimeString(),
                'assigned_by' => $createdBy?->name ?? 'System',
            ]
        );

        // Send WhatsApp notification if user has it enabled
        if ($user->canReceiveWhatsApp() && config('twilio.enabled')) {
            try {
                $twilioService = app(\App\Services\TwilioWhatsAppService::class);

                if ($twilioService->isConfigured()) {
                    $result = $twilioService->sendTaskAssignedNotification(
                        $user->whatsapp_number,
                        $user->name,
                        $reminder->task_description,
                        $complaint->ticket_number,
                        $reminder->priority,
                        $reminder->reminder_datetime->format('M d, Y h:i A'),
                        $createdBy?->name ?? 'System'
                    );

                    if ($result['success']) {
                        \Log::info('[Twilio] Task assigned WhatsApp sent', [
                            'reminder_id' => $reminder->id,
                            'user_id' => $user->id,
                        ]);
                    }
                }
            } catch (\Exception $e) {
                \Log::error('[Twilio] Error sending task assigned WhatsApp: ' . $e->getMessage());
            }
        }
    }

    /**
     * Update an existing reminder
     */
    public function update(Request $request, Complaint $complaint, TicketReminder $reminder)
    {
        // SECURITY: Authorise access and verify ownership
        $this->authoriseTicketAccess($complaint);
        $this->verifyReminderOwnership($complaint, $reminder);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'task_description' => 'required|string|max:1000',
            'reminder_datetime' => 'required|date',
            'priority' => 'required|in:low,medium,high',
            'notes' => 'nullable|string|max:2000',
        ]);

        // Sanitise text inputs
        $validated['task_description'] = strip_tags($validated['task_description']);
        if (!empty($validated['notes'])) {
            $validated['notes'] = strip_tags($validated['notes']);
        }

        $reminder->update($validated);

        return redirect()
            ->route('complaints.show', $reminder->complaint->ticket_number)
            ->with('success', 'Task reminder updated successfully.');
    }

    /**
     * Mark a reminder as completed
     */
    public function complete(Complaint $complaint, TicketReminder $reminder)
    {
        // SECURITY: Authorise access and verify ownership
        $this->authoriseTicketAccess($complaint);
        $this->verifyReminderOwnership($complaint, $reminder);

        $reminder->markCompleted(Auth::id());

        return redirect()
            ->route('complaints.show', $reminder->complaint->ticket_number)
            ->with('success', 'Task marked as completed.');
    }

    /**
     * Cancel a reminder
     */
    public function cancel(Complaint $complaint, TicketReminder $reminder)
    {
        // SECURITY: Authorise access and verify ownership
        $this->authoriseTicketAccess($complaint);
        $this->verifyReminderOwnership($complaint, $reminder);

        $reminder->update(['status' => 'cancelled']);

        return redirect()
            ->route('complaints.show', $reminder->complaint->ticket_number)
            ->with('success', 'Task reminder cancelled.');
    }

    /**
     * Delete a reminder
     */
    public function destroy(Complaint $complaint, TicketReminder $reminder)
    {
        // SECURITY: Authorise access and verify ownership
        $this->authoriseTicketAccess($complaint);
        $this->verifyReminderOwnership($complaint, $reminder);

        $ticketNumber = $reminder->complaint->ticket_number;
        $reminder->delete();

        return redirect()
            ->route('complaints.show', $ticketNumber)
            ->with('success', 'Task reminder deleted.');
    }

    /**
     * Get reminders for a specific user (for dashboard/profile)
     */
    public function userReminders(Request $request)
    {
        $user = Auth::user();

        $reminders = TicketReminder::with(['complaint', 'creator'])
            ->where('user_id', $user->id)
            ->pending()
            ->orderBy('reminder_datetime', 'asc')
            ->get();

        return response()->json($reminders);
    }

    /**
     * Snooze a reminder (postpone by X hours)
     */
    public function snooze(Request $request, Complaint $complaint, TicketReminder $reminder)
    {
        // SECURITY: Authorise access and verify ownership
        $this->authoriseTicketAccess($complaint);
        $this->verifyReminderOwnership($complaint, $reminder);

        $validated = $request->validate([
            'hours' => 'required|integer|min:1|max:72',
        ]);

        $reminder->update([
            'reminder_datetime' => $reminder->reminder_datetime->addHours($validated['hours']),
            'notification_sent' => false, // Reset notification flag
        ]);

        return redirect()
            ->route('complaints.show', $reminder->complaint->ticket_number)
            ->with('success', 'Reminder snoozed for ' . $validated['hours'] . ' hour(s).');
    }
}
