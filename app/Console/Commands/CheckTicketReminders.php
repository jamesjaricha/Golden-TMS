<?php

namespace App\Console\Commands;

use App\Models\TicketReminder;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckTicketReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminders:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for due ticket reminders and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for due reminders...');

        // Get all due reminders that haven't been notified yet
        $dueReminders = TicketReminder::with(['complaint', 'user', 'creator'])
            ->due()
            ->get();

        if ($dueReminders->isEmpty()) {
            $this->info('No due reminders found.');
            return 0;
        }

        $this->info("Found {$dueReminders->count()} due reminder(s).");

        foreach ($dueReminders as $reminder) {
            try {
                // Send notification to assigned user
                NotificationService::notifyTaskReminder($reminder);

                // Mark notification as sent
                $reminder->markNotificationSent();

                $this->info("✓ Sent reminder for task: {$reminder->task_description} (Ticket: {$reminder->complaint->ticket_number})");

                Log::info('[Reminders] Notification sent', [
                    'reminder_id' => $reminder->id,
                    'ticket_number' => $reminder->complaint->ticket_number,
                    'user_id' => $reminder->user_id,
                    'task' => $reminder->task_description,
                ]);
            } catch (\Exception $e) {
                $this->error("✗ Failed to send reminder ID {$reminder->id}: {$e->getMessage()}");

                Log::error('[Reminders] Failed to send notification', [
                    'reminder_id' => $reminder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info('Reminder check completed.');
        return 0;
    }
}
