<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\DepartmentController;

class NotificationService
{
    /**
     * Create a notification for a user
     */
    public static function create(int $userId, string $type, string $title, string $message, ?string $url = null, ?array $data = null): Notification
    {
        return Notification::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'url' => $url,
            'data' => $data,
        ]);
    }

    /**
     * Notify user about ticket assignment
     */
    public static function notifyTicketAssigned($complaint, User $assignedUser)
    {
        // Create in-app notification
        self::create(
            $assignedUser->id,
            'ticket_assigned',
            'New Ticket Assigned',
            "Ticket #{$complaint->ticket_number} has been assigned to you",
            route('complaints.show', $complaint),
            ['ticket_id' => $complaint->id, 'ticket_number' => $complaint->ticket_number]
        );

        // Send email notification
        try {
            $managers = User::whereIn('role', ['super_admin', 'manager'])->get();
            $ccEmails = $managers->pluck('email')->toArray();

            Mail::send('emails.ticket-assigned', [
                'complaint' => $complaint,
                'assignedUser' => $assignedUser,
            ], function ($message) use ($assignedUser, $ccEmails, $complaint) {
                $message->to($assignedUser->email)
                    ->subject('Ticket Assigned: ' . $complaint->ticket_number);

                if (!empty($ccEmails)) {
                    $message->cc($ccEmails);
                }
            });
        } catch (\Exception $e) {
            Log::error('Failed to send ticket assignment email: ' . $e->getMessage());
        }

        // Send WhatsApp notification to customer via Twilio
        self::sendWhatsAppTicketCreated($complaint);
    }

    /**
     * Send WhatsApp notification when ticket is created (via Twilio)
     * Uses the ticket_created template to notify client their ticket was received
     */
    public static function sendWhatsAppTicketCreated(Complaint $complaint)
    {
        if (!config('twilio.enabled') || !config('twilio.notifications.send_on_create', true)) {
            Log::info('[Twilio] Disabled or send_on_create is false');
            return;
        }

        try {
            $twilioService = app(TwilioWhatsAppService::class);

            if (!$twilioService->isConfigured()) {
                Log::info('[Twilio] Service not configured, skipping notification');
                return;
            }

            // Check if we have a specific ticket_created template
            $templateSid = config('twilio.templates.ticket_created');

            if (!empty($templateSid)) {
                // Use dedicated ticket_created template
                $result = $twilioService->sendTicketCreatedNotification(
                    $complaint->phone_number,
                    $complaint->full_name,
                    $complaint->ticket_number,
                    $complaint->department->name ?? 'N/A',
                    ucfirst($complaint->priority)
                );
            } else {
                // Fall back to using status update notification (in_progress template)
                // This simulates a status change from "New" to "In Progress"
                $result = $twilioService->sendTicketUpdatedNotification(
                    $complaint->phone_number,
                    $complaint->full_name,
                    $complaint->ticket_number,
                    'New',
                    'In Progress'
                );
            }

            if ($result['success']) {
                Log::info('[Twilio] Ticket created notification sent', [
                    'ticket_number' => $complaint->ticket_number,
                    'phone' => $complaint->phone_number,
                    'message_sid' => $result['message_sid'] ?? null,
                ]);
            } else {
                Log::warning('[Twilio] Failed to send ticket created notification', [
                    'ticket_number' => $complaint->ticket_number,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[Twilio] Error sending ticket created notification: ' . $e->getMessage(), [
                'ticket_number' => $complaint->ticket_number ?? 'unknown',
            ]);
        }
    }

    /**
     * Send WhatsApp notification when ticket status changes (via Twilio)
     */
    public static function sendWhatsAppStatusUpdate(Complaint $complaint, string $oldStatus, string $newStatus)
    {
        if (!config('twilio.enabled') || !config('twilio.notifications.send_on_status_change', true)) {
            Log::info('[Twilio] Disabled or send_on_status_change is false');
            return;
        }

        try {
            $twilioService = app(TwilioWhatsAppService::class);

            if (!$twilioService->isConfigured()) {
                Log::info('[Twilio] Service not configured, skipping status update notification');
                return;
            }

            // Check if resolved
            if ($newStatus === 'resolved' && config('twilio.notifications.send_on_resolved', true)) {
                $result = $twilioService->sendTicketResolvedNotification(
                    $complaint->phone_number,
                    $complaint->full_name,
                    $complaint->ticket_number
                );
            } elseif ($newStatus === 'partial_closed') {
                // Skip notification for partial closure - internal status only
                Log::info('[Twilio] Skipping notification for partial_closed status (internal only)', [
                    'ticket_number' => $complaint->ticket_number,
                ]);
                return;
            } else {
                $result = $twilioService->sendTicketUpdatedNotification(
                    $complaint->phone_number,
                    $complaint->full_name,
                    $complaint->ticket_number,
                    ucwords(str_replace('_', ' ', $oldStatus)),
                    ucwords(str_replace('_', ' ', $newStatus))
                );
            }

            if ($result['success']) {
                Log::info('[Twilio] Status update notification sent', [
                    'ticket_number' => $complaint->ticket_number,
                    'phone' => $complaint->phone_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'message_sid' => $result['message_sid'] ?? null,
                ]);
            } else {
                Log::warning('[Twilio] Failed to send status update notification', [
                    'ticket_number' => $complaint->ticket_number,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[Twilio] Error sending status update notification: ' . $e->getMessage(), [
                'ticket_number' => $complaint->ticket_number ?? 'unknown',
            ]);
        }
    }

    /**
     * Notify user about ticket update
     */
    public static function notifyTicketUpdated($complaint, User $user, string $updateType)
    {
        self::create(
            $user->id,
            'ticket_updated',
            'Ticket Updated',
            "Ticket #{$complaint->ticket_number} has been updated: {$updateType}",
            route('complaints.show', $complaint),
            ['ticket_id' => $complaint->id, 'ticket_number' => $complaint->ticket_number]
        );
    }

    /**
     * Get unread notifications count for a user
     */
    public static function getUnreadCount(int $userId): int
    {
        return Notification::where('user_id', $userId)
            ->where('read', false)
            ->count();
    }

    /**
     * Get recent notifications for a user
     */
    public static function getRecent(int $userId, int $limit = 10)
    {
        return Notification::where('user_id', $userId)
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all notifications as read
     */
    public static function markAllAsRead(int $userId)
    {
        Notification::where('user_id', $userId)
            ->where('read', false)
            ->update([
                'read' => true,
                'read_at' => now(),
            ]);
    }

    /**
     * Notify user about a task reminder
     */
    public static function notifyTaskReminder($reminder)
    {
        $complaint = $reminder->complaint;
        $user = $reminder->user;

        // Priority emoji mapping
        $priorityEmoji = [
            'low' => '📋',
            'medium' => '⏰',
            'high' => '🔥',
        ];

        $emoji = $priorityEmoji[$reminder->priority] ?? '⏰';
        $overdueText = $reminder->isOverdue() ? ' (OVERDUE)' : '';

        // Create in-app notification
        self::create(
            $user->id,
            'task_reminder',
            "{$emoji} Task Reminder{$overdueText}",
            $reminder->task_description,
            route('complaints.show', $complaint->ticket_number),
            [
                'reminder_id' => $reminder->id,
                'ticket_id' => $complaint->id,
                'ticket_number' => $complaint->ticket_number,
                'priority' => $reminder->priority,
                'due_at' => $reminder->reminder_datetime->toDateTimeString(),
                'is_overdue' => $reminder->isOverdue(),
            ]
        );

        // Send WhatsApp notification if user has it enabled
        self::sendWhatsAppTaskReminder($reminder);
    }

    /**
     * Send WhatsApp notification for task reminder
     */
    public static function sendWhatsAppTaskReminder($reminder)
    {
        $user = $reminder->user;

        // Check if user can receive WhatsApp notifications
        if (!$user->canReceiveWhatsApp()) {
            Log::info('[Twilio] User has no WhatsApp number or notifications disabled', [
                'user_id' => $user->id,
                'reminder_id' => $reminder->id,
            ]);
            return;
        }

        if (!config('twilio.enabled')) {
            Log::info('[Twilio] Disabled, skipping task reminder WhatsApp');
            return;
        }

        try {
            $twilioService = app(TwilioWhatsAppService::class);

            if (!$twilioService->isConfigured()) {
                Log::info('[Twilio] Service not configured, skipping task reminder WhatsApp');
                return;
            }

            $result = $twilioService->sendTaskReminderNotification(
                $user->whatsapp_number,
                $user->name,
                $reminder->task_description,
                $reminder->complaint->ticket_number,
                $reminder->priority,
                $reminder->isOverdue()
            );

            if ($result['success']) {
                Log::info('[Twilio] Task reminder WhatsApp sent', [
                    'reminder_id' => $reminder->id,
                    'user_id' => $user->id,
                    'phone' => $user->whatsapp_number,
                    'message_sid' => $result['message_sid'] ?? null,
                ]);
            } else {
                Log::warning('[Twilio] Failed to send task reminder WhatsApp', [
                    'reminder_id' => $reminder->id,
                    'user_id' => $user->id,
                    'error' => $result['error'] ?? 'Unknown error',
                ]);
            }
        } catch (\Exception $e) {
            Log::error('[Twilio] Error sending task reminder WhatsApp: ' . $e->getMessage(), [
                'reminder_id' => $reminder->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
