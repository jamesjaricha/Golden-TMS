<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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
        'branch_id',
        'employer_id',
        'payment_method_id',
        'department_id',
        'pending_department',
        'completed_department',
        'complaint_text',
        'status',
        'priority',
        'captured_by',
        'assigned_to',
        'resolved_at',
        'partial_closed_at',
        'closed_at',
        'resolution_notes',
        'partial_close_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'partial_closed_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the route key for implicit route model binding.
     * Use ticket_number instead of id for cleaner URLs
     */
    public function getRouteKeyName(): string
    {
        return 'ticket_number';
    }

    /**
     * All available statuses
     */
    public const STATUSES = [
        'pending' => 'Pending',
        'assigned' => 'Assigned',
        'in_progress' => 'In Progress',
        'partial_closed' => 'Partial Closed',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
        'escalated' => 'Escalated',
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
     * Format: TKT-YYYYMMDD-HHMM[A-Z]
     */
    public static function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('Ymd');
        $time = now()->format('Hi'); // HHMM format (24-hour)

        // Try letters A through Z until we find a unique ticket number
        foreach (range('A', 'Z') as $letter) {
            $ticketNumber = "{$prefix}-{$date}-{$time}{$letter}";

            // Check if this ticket number already exists
            if (!self::where('ticket_number', $ticketNumber)->exists()) {
                return $ticketNumber;
            }
        }

        // Fallback: if all A-Z are taken (extremely unlikely), use milliseconds
        $milliseconds = substr((string) microtime(true) * 1000, -4);
        return "{$prefix}-{$date}-{$time}{$milliseconds}";
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
     * Branch relationship
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Employer relationship
     */
    public function employer(): BelongsTo
    {
        return $this->belongsTo(Employer::class);
    }

    /**
     * Payment Method relationship
     */
    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Department relationship
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeByPriority(Builder $query, ?string $priority): Builder
    {
        return $priority ? $query->where('priority', $priority) : $query;
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate) {
            $query->where('created_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $query->where('created_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
        }
        return $query;
    }

    /**
     * Scope for user role-based access
     */
    public function scopeForUser(Builder $query, $user): Builder
    {
        if ($user->role === 'user') {
            return $query->where('captured_by', $user->id);
        }
        return $query; // Admins, managers, agents see all
    }

    /**
     * Scope for searching tickets
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function($q) use ($search) {
            $q->where('ticket_number', 'like', "%{$search}%")
              ->orWhere('policy_number', 'like', "%{$search}%")
              ->orWhere('full_name', 'like', "%{$search}%")
              ->orWhere('phone_number', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for filtering by branch
     */
    public function scopeByBranch(Builder $query, ?int $branchId): Builder
    {
        return $branchId ? $query->where('branch_id', $branchId) : $query;
    }

    /**
     * Legacy scope for backward compatibility
     * @deprecated Use byStatus() instead
     */
    public function scopeStatus($query, string $status): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('status', $status);
    }

    /**
     * Legacy scope for backward compatibility
     * @deprecated Use byPriority() instead
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
     * Check if complaint is partially closed
     */
    public function isPartialClosed(): bool
    {
        return $this->status === 'partial_closed';
    }

    /**
     * Get the status label (human readable)
     */
    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucwords(str_replace('_', ' ', $this->status));
    }

    /**
     * Get status badge color class
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'bg-yellow-100 text-yellow-800',
            'assigned' => 'bg-blue-100 text-blue-800',
            'in_progress' => 'bg-indigo-100 text-indigo-800',
            'partial_closed' => 'bg-orange-100 text-orange-800',
            'resolved' => 'bg-green-100 text-green-800',
            'closed' => 'bg-gray-100 text-gray-800',
            'escalated' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get the comments for the complaint.
     */
    public function comments()
    {
        return $this->hasMany(ComplaintComment::class)->orderBy('created_at', 'desc');
    }
}
