<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    use HasFactory;

    protected $table = 'whatsapp_messages';

    protected $fillable = [
        'message_sid',
        'from_number',
        'to_number',
        'body',
        'profile_name',
        'media_url',
        'media_type',
        'status',
        'converted_to_ticket_id',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];

    /**
     * Get the ticket this message was converted to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'converted_to_ticket_id');
    }

    /**
     * Get the user who processed this message
     */
    public function processedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for new messages
     */
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    /**
     * Scope for unprocessed messages
     */
    public function scopeUnprocessed($query)
    {
        return $query->whereIn('status', ['new', 'viewed']);
    }

    /**
     * Get formatted phone number
     */
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->from_number;
        // Remove whatsapp: prefix if present
        $phone = str_replace('whatsapp:', '', $phone);
        return $phone;
    }

    /**
     * Get display name (profile name or phone)
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->profile_name ?: $this->formatted_phone;
    }

    /**
     * Mark as viewed
     */
    public function markAsViewed(): void
    {
        if ($this->status === 'new') {
            $this->update(['status' => 'viewed']);
        }
    }

    /**
     * Convert to ticket
     */
    public function convertToTicket(Complaint $complaint, User $processedBy): void
    {
        $this->update([
            'status' => 'converted',
            'converted_to_ticket_id' => $complaint->id,
            'processed_by' => $processedBy->id,
            'processed_at' => now(),
        ]);
    }

    /**
     * Archive message
     */
    public function archive(User $processedBy): void
    {
        $this->update([
            'status' => 'archived',
            'processed_by' => $processedBy->id,
            'processed_at' => now(),
        ]);
    }
}
