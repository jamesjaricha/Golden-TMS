<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Jenssegers\Agent\Agent;

class AuditLogService
{
    /**
     * Log an action with full context
     */
    public static function log(
        string $action,
        string $category,
        string $description,
        ?Model $model = null,
        array $oldValues = [],
        array $newValues = [],
        array $metadata = [],
        string $status = 'success',
        ?string $failureReason = null
    ): AuditLog {
        $user = Auth::user();
        $request = Request::instance();

        // Parse user agent for device info
        $deviceInfo = self::parseUserAgent($request->userAgent());

        // Determine changed fields
        $changedFields = [];
        if (!empty($oldValues) && !empty($newValues)) {
            $changedFields = self::getChangedFields($oldValues, $newValues);
        }

        return AuditLog::create([
            // User info (stored separately for persistence)
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_email' => $user?->email,
            'user_role' => $user?->role,

            // Action info
            'action' => $action,
            'action_category' => $category,

            // Model info
            'auditable_type' => $model ? get_class($model) : null,
            'auditable_id' => $model?->id,
            'auditable_identifier' => self::getModelIdentifier($model),

            // Changes
            'old_values' => !empty($oldValues) ? $oldValues : null,
            'new_values' => !empty($newValues) ? $newValues : null,
            'changed_fields' => !empty($changedFields) ? $changedFields : null,

            // Description & metadata
            'description' => $description,
            'metadata' => !empty($metadata) ? $metadata : null,

            // Request info
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'device_type' => $deviceInfo['device_type'],
            'browser' => $deviceInfo['browser'],
            'platform' => $deviceInfo['platform'],

            // Session
            'session_id' => session()->getId(),

            // Status
            'status' => $status,
            'failure_reason' => $failureReason,
        ]);
    }

    /**
     * Log a model creation
     */
    public static function logCreate(Model $model, string $description = null): AuditLog
    {
        $category = self::getCategoryForModel($model);
        $description = $description ?? "Created " . class_basename($model) . " #{$model->id}";

        return self::log(
            AuditLog::ACTION_CREATE,
            $category,
            $description,
            $model,
            [],
            $model->getAttributes()
        );
    }

    /**
     * Log a model update with before/after values
     */
    public static function logUpdate(Model $model, array $oldValues, string $description = null): AuditLog
    {
        $category = self::getCategoryForModel($model);
        $newValues = $model->getAttributes();
        $changedFields = self::getChangedFields($oldValues, $newValues);

        $description = $description ?? "Updated " . class_basename($model) . " #{$model->id}";

        // Only include relevant fields in old/new values
        $relevantOld = array_intersect_key($oldValues, array_flip($changedFields));
        $relevantNew = array_intersect_key($newValues, array_flip($changedFields));

        return self::log(
            AuditLog::ACTION_UPDATE,
            $category,
            $description,
            $model,
            $relevantOld,
            $relevantNew
        );
    }

    /**
     * Log a status change specifically
     */
    public static function logStatusChange(Model $model, string $oldStatus, string $newStatus): AuditLog
    {
        $category = self::getCategoryForModel($model);
        $identifier = self::getModelIdentifier($model);

        return self::log(
            AuditLog::ACTION_STATUS_CHANGE,
            $category,
            "Changed status of {$identifier} from '{$oldStatus}' to '{$newStatus}'",
            $model,
            ['status' => $oldStatus],
            ['status' => $newStatus]
        );
    }

    /**
     * Log ticket assignment/takeover
     */
    public static function logAssignment(
        Model $ticket,
        ?User $previousAssignee,
        User $newAssignee,
        bool $isTakeover = false
    ): AuditLog {
        $action = $isTakeover ? AuditLog::ACTION_TAKEOVER : AuditLog::ACTION_ASSIGN;
        $currentUser = Auth::user();

        $description = $isTakeover
            ? "Ticket {$ticket->ticket_number} taken over from " . ($previousAssignee?->name ?? 'Unassigned') . " to {$newAssignee->name} by {$currentUser->name}"
            : "Assigned ticket {$ticket->ticket_number} to {$newAssignee->name}";

        return self::log(
            $action,
            AuditLog::CATEGORY_TICKET,
            $description,
            $ticket,
            ['assigned_to' => $previousAssignee?->id, 'assigned_to_name' => $previousAssignee?->name],
            ['assigned_to' => $newAssignee->id, 'assigned_to_name' => $newAssignee->name],
            ['performed_by' => $currentUser->name]
        );
    }

    /**
     * Log a comment added
     */
    public static function logComment(Model $ticket, string $commentPreview): AuditLog
    {
        return self::log(
            AuditLog::ACTION_COMMENT,
            AuditLog::CATEGORY_TICKET,
            "Added comment to ticket {$ticket->ticket_number}",
            $ticket,
            [],
            [],
            ['comment_preview' => substr($commentPreview, 0, 100)]
        );
    }

    /**
     * Log successful login
     */
    public static function logLogin(User $user): AuditLog
    {
        // Temporarily set the user for logging
        Auth::setUser($user);

        return self::log(
            AuditLog::ACTION_LOGIN,
            AuditLog::CATEGORY_AUTH,
            "User {$user->name} logged in successfully",
            $user
        );
    }

    /**
     * Log logout
     */
    public static function logLogout(): AuditLog
    {
        $user = Auth::user();

        return self::log(
            AuditLog::ACTION_LOGOUT,
            AuditLog::CATEGORY_AUTH,
            "User {$user->name} logged out",
            $user
        );
    }

    /**
     * Log failed login attempt
     */
    public static function logFailedLogin(string $email, string $reason = 'Invalid credentials'): AuditLog
    {
        return self::log(
            AuditLog::ACTION_LOGIN_FAILED,
            AuditLog::CATEGORY_AUTH,
            "Failed login attempt for email: {$email}",
            null,
            [],
            [],
            ['attempted_email' => $email],
            'failed',
            $reason
        );
    }

    /**
     * Log data export
     */
    public static function logExport(
        string $exportType,
        string $format,
        array $filters = [],
        int $recordCount = 0
    ): AuditLog {
        $user = Auth::user();

        return self::log(
            AuditLog::ACTION_EXPORT,
            AuditLog::CATEGORY_REPORT,
            "{$user->name} exported {$recordCount} {$exportType} records to {$format}",
            null,
            [],
            [],
            [
                'export_type' => $exportType,
                'format' => $format,
                'filters' => $filters,
                'record_count' => $recordCount,
            ]
        );
    }

    /**
     * Log report generation
     */
    public static function logReportGenerated(string $reportType, array $parameters = []): AuditLog
    {
        $user = Auth::user();

        return self::log(
            AuditLog::ACTION_VIEW,
            AuditLog::CATEGORY_REPORT,
            "{$user->name} generated {$reportType} report",
            null,
            [],
            [],
            [
                'report_type' => $reportType,
                'parameters' => $parameters,
            ]
        );
    }

    /**
     * Log viewing a record
     */
    public static function logView(Model $model): AuditLog
    {
        $category = self::getCategoryForModel($model);
        $identifier = self::getModelIdentifier($model);

        return self::log(
            AuditLog::ACTION_VIEW,
            $category,
            "Viewed {$identifier}",
            $model
        );
    }

    /**
     * Get changed fields between old and new values
     */
    protected static function getChangedFields(array $oldValues, array $newValues): array
    {
        $changedFields = [];

        // Fields to ignore in comparison
        $ignoredFields = ['updated_at', 'created_at', 'remember_token'];

        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            if (in_array($key, $ignoredFields)) {
                continue;
            }

            $oldValue = $oldValues[$key] ?? null;
            $newValue = $newValues[$key] ?? null;

            if ($oldValue !== $newValue) {
                $changedFields[] = $key;
            }
        }

        return $changedFields;
    }

    /**
     * Get a human-readable identifier for a model
     */
    protected static function getModelIdentifier(?Model $model): ?string
    {
        if (!$model) {
            return null;
        }

        // Check for common identifier fields
        if (isset($model->ticket_number)) {
            return "Ticket #{$model->ticket_number}";
        }

        if (isset($model->email)) {
            return "User {$model->name} ({$model->email})";
        }

        if (isset($model->name)) {
            return $model->name;
        }

        return class_basename($model) . " #{$model->id}";
    }

    /**
     * Get category for a model type
     */
    protected static function getCategoryForModel(Model $model): string
    {
        return match (class_basename($model)) {
            'Complaint' => AuditLog::CATEGORY_TICKET,
            'User' => AuditLog::CATEGORY_USER,
            default => AuditLog::CATEGORY_SYSTEM,
        };
    }

    /**
     * Parse user agent to extract device info
     */
    protected static function parseUserAgent(?string $userAgent): array
    {
        if (!$userAgent) {
            return [
                'device_type' => 'unknown',
                'browser' => 'unknown',
                'platform' => 'unknown',
            ];
        }

        // Simple parsing without external package
        $deviceType = 'desktop';
        if (preg_match('/Mobile|Android|iPhone|iPad/i', $userAgent)) {
            $deviceType = preg_match('/iPad|Tablet/i', $userAgent) ? 'tablet' : 'mobile';
        }

        $browser = 'unknown';
        if (preg_match('/Firefox/i', $userAgent)) {
            $browser = 'Firefox';
        } elseif (preg_match('/Edg/i', $userAgent)) {
            $browser = 'Edge';
        } elseif (preg_match('/Chrome/i', $userAgent)) {
            $browser = 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent)) {
            $browser = 'Safari';
        } elseif (preg_match('/MSIE|Trident/i', $userAgent)) {
            $browser = 'Internet Explorer';
        }

        $platform = 'unknown';
        if (preg_match('/Windows/i', $userAgent)) {
            $platform = 'Windows';
        } elseif (preg_match('/Mac/i', $userAgent)) {
            $platform = 'macOS';
        } elseif (preg_match('/Linux/i', $userAgent)) {
            $platform = 'Linux';
        } elseif (preg_match('/Android/i', $userAgent)) {
            $platform = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/i', $userAgent)) {
            $platform = 'iOS';
        }

        return [
            'device_type' => $deviceType,
            'browser' => $browser,
            'platform' => $platform,
        ];
    }
}
