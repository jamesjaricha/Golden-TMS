<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'user_name',
        'user_email',
        'user_role',
        'action',
        'action_category',
        'auditable_type',
        'auditable_id',
        'auditable_identifier',
        'old_values',
        'new_values',
        'changed_fields',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'device_type',
        'browser',
        'platform',
        'session_id',
        'status',
        'failure_reason',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
        'metadata' => 'array',
    ];

    /**
     * Action categories
     */
    const CATEGORY_AUTH = 'auth';
    const CATEGORY_TICKET = 'ticket';
    const CATEGORY_USER = 'user';
    const CATEGORY_REPORT = 'report';
    const CATEGORY_SYSTEM = 'system';

    /**
     * Common actions
     */
    const ACTION_LOGIN = 'login';
    const ACTION_LOGOUT = 'logout';
    const ACTION_LOGIN_FAILED = 'login_failed';
    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';
    const ACTION_VIEW = 'view';
    const ACTION_EXPORT = 'export';
    const ACTION_ASSIGN = 'assign';
    const ACTION_TAKEOVER = 'takeover';
    const ACTION_STATUS_CHANGE = 'status_change';
    const ACTION_COMMENT = 'comment';

    /**
     * Get the user who performed this action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the auditable model
     */
    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to filter by action category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('action_category', $category);
    }

    /**
     * Scope to filter by action
     */
    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by model
     */
    public function scopeForModel($query, string $modelType, int $modelId)
    {
        return $query->where('auditable_type', $modelType)
                     ->where('auditable_id', $modelId);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Get a human-readable summary of changes
     */
    public function getChangeSummaryAttribute(): string
    {
        if (empty($this->changed_fields)) {
            return $this->description;
        }

        $changes = [];
        foreach ($this->changed_fields as $field) {
            $old = $this->old_values[$field] ?? 'empty';
            $new = $this->new_values[$field] ?? 'empty';
            $changes[] = "{$field}: '{$old}' → '{$new}'";
        }

        return implode(', ', $changes);
    }
}
