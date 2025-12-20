<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogService
{
    /**
     * Log an activity
     */
    public static function log(string $action, string $description, $model = null, array $properties = []): ActivityLog
    {
        return ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->id : null,
            'description' => $description,
            'properties' => !empty($properties) ? json_encode($properties) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Log ticket creation
     */
    public static function logTicketCreated($complaint): ActivityLog
    {
        return self::log(
            'ticket_created',
            "Created ticket {$complaint->ticket_number} for {$complaint->full_name}",
            $complaint,
            [
                'ticket_number' => $complaint->ticket_number,
                'priority' => $complaint->priority,
                'status' => $complaint->status,
            ]
        );
    }

    /**
     * Log ticket assignment
     */
    public static function logTicketAssigned($complaint, $assignedTo): ActivityLog
    {
        return self::log(
            'ticket_assigned',
            "Assigned ticket {$complaint->ticket_number} to {$assignedTo->name}",
            $complaint,
            [
                'ticket_number' => $complaint->ticket_number,
                'assigned_to_id' => $assignedTo->id,
                'assigned_to_name' => $assignedTo->name,
            ]
        );
    }

    /**
     * Log ticket status update
     */
    public static function logTicketStatusUpdate($complaint, $oldStatus, $newStatus): ActivityLog
    {
        return self::log(
            'ticket_status_updated',
            "Updated ticket {$complaint->ticket_number} status from {$oldStatus} to {$newStatus}",
            $complaint,
            [
                'ticket_number' => $complaint->ticket_number,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]
        );
    }

    /**
     * Log user creation
     */
    public static function logUserCreated($user): ActivityLog
    {
        return self::log(
            'user_created',
            "Created new user: {$user->name} ({$user->role})",
            $user,
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
                'user_role' => $user->role,
            ]
        );
    }

    /**
     * Log user update
     */
    public static function logUserUpdated($user, $changes): ActivityLog
    {
        return self::log(
            'user_updated',
            "Updated user: {$user->name}",
            $user,
            ['changes' => $changes]
        );
    }

    /**
     * Log user deletion
     */
    public static function logUserDeleted($user): ActivityLog
    {
        return self::log(
            'user_deleted',
            "Deleted user: {$user->name}",
            $user,
            [
                'user_name' => $user->name,
                'user_email' => $user->email,
            ]
        );
    }

    /**
     * Log login
     */
    public static function logLogin(): ActivityLog
    {
        return self::log(
            'user_login',
            'User logged in',
            Auth::user()
        );
    }

    /**
     * Log logout
     */
    public static function logLogout(): ActivityLog
    {
        return self::log(
            'user_logout',
            'User logged out',
            Auth::user()
        );
    }
}
