<?php

namespace App\Observers;

use App\Models\Complaint;
use App\Services\AuditLogService;

class ComplaintObserver
{
    /**
     * Store the original attributes before update
     */
    protected static array $originalAttributes = [];

    /**
     * Handle the Complaint "created" event.
     */
    public function created(Complaint $complaint): void
    {
        AuditLogService::logCreate(
            $complaint,
            "Created ticket {$complaint->ticket_number} for {$complaint->full_name}"
        );
    }

    /**
     * Handle the Complaint "updating" event (before save).
     * Store original values for comparison.
     */
    public function updating(Complaint $complaint): void
    {
        // Store original attributes before update
        self::$originalAttributes[$complaint->id] = $complaint->getOriginal();
    }

    /**
     * Handle the Complaint "updated" event.
     */
    public function updated(Complaint $complaint): void
    {
        $originalAttributes = self::$originalAttributes[$complaint->id] ?? [];

        if (empty($originalAttributes)) {
            return;
        }

        // Check if status changed specifically
        if (isset($originalAttributes['status']) && $originalAttributes['status'] !== $complaint->status) {
            AuditLogService::logStatusChange(
                $complaint,
                $originalAttributes['status'],
                $complaint->status
            );
        }

        // Log general update with all changes
        AuditLogService::logUpdate(
            $complaint,
            $originalAttributes,
            "Updated ticket {$complaint->ticket_number}"
        );

        // Clean up stored attributes
        unset(self::$originalAttributes[$complaint->id]);
    }

    /**
     * Handle the Complaint "retrieved" event.
     * Only log detailed views, not list queries.
     */
    public function retrieved(Complaint $complaint): void
    {
        // We'll handle view logging in the controller instead
        // to avoid logging every query
    }
}
