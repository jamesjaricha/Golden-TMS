<?php

namespace App\Listeners;

use App\Services\AuditLogService;
use Illuminate\Auth\Events\Failed;

class LogFailedLogin
{
    /**
     * Handle the event.
     */
    public function handle(Failed $event): void
    {
        AuditLogService::logFailedLogin(
            $event->credentials['email'] ?? 'unknown',
            'Invalid credentials'
        );
    }
}
