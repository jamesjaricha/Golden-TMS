<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketReminder extends Model
{
    protected $fillable = [
        'complaint_id',
        'user_id',
        'created_by',
        'task_description',
        'reminder_datetime',
        'status',
        'priority',
        'notes',
        'completed_at',
        'completed_by',
        'notification_sent',
    ];

    protected $casts = [
        'reminder_datetime' => 'datetime',
        'completed_at' => 'datetime',
        'notification_sent' => 'boolean',
    ];

    /**
     * Get the complaint (ticket) this reminder belongs to
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * Get the user who should be reminded
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created this reminder
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who completed this task
     */
    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Scope for pending reminders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for due reminders (past reminder datetime)
     */
    public function scopeDue($query)
    {
        return $query->where('reminder_datetime', '<=', now())
                    ->where('status', 'pending')
                    ->where('notification_sent', false);
    }

    /**
     * Scope for upcoming reminders (within next X hours)
     */
    public function scopeUpcoming($query, int $hours = 24)
    {
        return $query->where('reminder_datetime', '>', now())
                    ->where('reminder_datetime', '<=', now()->addHours($hours))
                    ->where('status', 'pending');
    }

    /**
     * Check if reminder is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'pending' && $this->reminder_datetime < now();
    }

    /**
     * Mark reminder as completed
     */
    public function markCompleted(int $userId): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'completed_by' => $userId,
        ]);
    }

    /**
     * Mark notification as sent
     */
    public function markNotificationSent(): bool
    {
        return $this->update(['notification_sent' => true]);
    }
}
