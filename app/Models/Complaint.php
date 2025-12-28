<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'policy_number',
        'full_name',
        'phone_number',
        'location',
        'visited_branch',
        'department',
        'complaint_text',
        'status',
        'priority',
        'captured_by',
        'assigned_to',
        'resolved_at',
        'closed_at',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($complaint) {
            if (empty($complaint->ticket_number)) {
                $complaint->ticket_number = self::generateTicketNumber();
            }
        });
    }

    /**
     * Generate unique ticket number
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $random = strtoupper(substr(md5(uniqid()), 0, 6));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Get the user who captured this complaint
     */
    public function capturedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by');
    }

    /**
     * Get the user assigned to this complaint
     */
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope for filtering by status
     */
    public function scopeStatus($query, string $status): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopePriority($query, string $priority): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('priority', $priority);
    }

    /**
     * Check if complaint is resolved
     */
    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }

    /**
     * Get the comments for the complaint.
     */
    public function comments()
    {
        return $this->hasMany(ComplaintComment::class)->orderBy('created_at', 'desc');
    }
}
