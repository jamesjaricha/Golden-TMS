<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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
}
