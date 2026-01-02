<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppConversation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     */
    protected $table = 'whatsapp_conversations';

    protected $fillable = [
        'phone_number',
        'agent_id',
        'current_step',
        'collected_data',
        'started_at',
        'last_activity_at',
        'completed_at',
        'created_ticket_id',
    ];

    protected $casts = [
        'collected_data' => 'array',
        'started_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Conversation steps in order
     */
    public const STEPS = [
        'idle',           // No active conversation
        'client_name',    // Asking for client's full name
        'client_phone',   // Asking for client's phone number
        'policy_number',  // Asking for policy number
        'employer',       // Asking for employer
        'payment_method', // Asking for payment method
        'location',       // Asking for client's location
        'branch',         // Asking which branch they visited
        'department',     // Asking which department
        'issue',          // Asking for issue description
        'priority',       // Asking for priority level
        'confirm',        // Confirming all details
        'completed',      // Ticket created
    ];

    /**
     * Get the agent who started this conversation
     */
    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Get the created ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Complaint::class, 'created_ticket_id');
    }

    /**
     * Check if conversation is active (not idle or completed)
     */
    public function isActive(): bool
    {
        return !in_array($this->current_step, ['idle', 'completed']);
    }

    /**
     * Check if conversation has expired (no activity for 30 minutes)
     */
    public function hasExpired(): bool
    {
        if (!$this->last_activity_at) {
            return true;
        }
        return $this->last_activity_at->diffInMinutes(now()) > 30;
    }

    /**
     * Get or create conversation for a phone number
     */
    public static function getOrCreate(string $phoneNumber, ?int $agentId = null): self
    {
        $conversation = self::where('phone_number', $phoneNumber)->first();

        if ($conversation && $conversation->hasExpired()) {
            // Reset expired conversation
            $conversation->update([
                'current_step' => 'idle',
                'collected_data' => null,
                'started_at' => null,
                'completed_at' => null,
            ]);
        }

        if (!$conversation) {
            $conversation = self::create([
                'phone_number' => $phoneNumber,
                'agent_id' => $agentId,
                'current_step' => 'idle',
            ]);
        }

        return $conversation;
    }

    /**
     * Start a new ticket creation wizard
     */
    public function startWizard(?int $agentId = null): void
    {
        $this->update([
            'agent_id' => $agentId ?? $this->agent_id,
            'current_step' => 'client_name',
            'collected_data' => [],
            'started_at' => now(),
            'last_activity_at' => now(),
            'completed_at' => null,
            'created_ticket_id' => null,
        ]);
    }

    /**
     * Move to the next step
     */
    public function nextStep(): void
    {
        $currentIndex = array_search($this->current_step, self::STEPS);
        if ($currentIndex !== false && $currentIndex < count(self::STEPS) - 1) {
            $this->update([
                'current_step' => self::STEPS[$currentIndex + 1],
                'last_activity_at' => now(),
            ]);
        }
    }

    /**
     * Go back to the previous step
     */
    public function previousStep(): void
    {
        $currentIndex = array_search($this->current_step, self::STEPS);
        if ($currentIndex !== false && $currentIndex > 1) { // Don't go back to 'idle'
            $this->update([
                'current_step' => self::STEPS[$currentIndex - 1],
                'last_activity_at' => now(),
            ]);
        }
    }

    /**
     * Set data for current step and move to next
     */
    public function setStepData(string $key, mixed $value): void
    {
        $data = $this->collected_data ?? [];
        $data[$key] = $value;
        $this->update([
            'collected_data' => $data,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Get collected data value
     */
    public function getData(string $key, mixed $default = null): mixed
    {
        return $this->collected_data[$key] ?? $default;
    }

    /**
     * Cancel the current conversation
     */
    public function cancel(): void
    {
        $this->update([
            'current_step' => 'idle',
            'collected_data' => null,
            'started_at' => null,
            'last_activity_at' => now(),
        ]);
    }

    /**
     * Complete the conversation with a ticket
     */
    public function complete(int $ticketId): void
    {
        $this->update([
            'current_step' => 'completed',
            'completed_at' => now(),
            'created_ticket_id' => $ticketId,
            'last_activity_at' => now(),
        ]);
    }
}
