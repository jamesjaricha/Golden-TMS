<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppService
{
    protected string $accountSid;
    protected string $authToken;
    protected string $fromNumber;
    protected bool $enabled;
    protected bool $loggingEnabled;

    public function __construct()
    {
        $this->accountSid = config('twilio.account_sid', '');
        $this->authToken = config('twilio.auth_token', '');
        $this->fromNumber = config('twilio.whatsapp_from', '');
        $this->enabled = config('twilio.enabled', false);
        $this->loggingEnabled = config('twilio.logging_enabled', true);
    }

    /**
     * Check if Twilio WhatsApp is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Check if Twilio is properly configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->accountSid)
            && !empty($this->authToken)
            && !empty($this->fromNumber);
    }

    /**
     * Get configuration status for dashboard
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->enabled,
            'configured' => $this->isConfigured(),
            'account_sid' => !empty($this->accountSid) ? substr($this->accountSid, 0, 10) . '...' : 'Not set',
            'from_number' => $this->fromNumber ?: 'Not set',
        ];
    }

    /**
     * Format phone number for WhatsApp
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove any non-numeric characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        // Ensure it starts with country code (no +)
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }

        // Add whatsapp: prefix
        return 'whatsapp:+' . $phone;
    }

    /**
     * Send a WhatsApp message using Twilio
     */
    public function sendMessage(string $to, string $message): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'Twilio WhatsApp is not enabled',
            ];
        }

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio is not properly configured',
            ];
        }

        $toFormatted = $this->formatPhoneNumber($to);
        $fromFormatted = 'whatsapp:' . $this->fromNumber;

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";

        $this->log('info', 'Sending Twilio WhatsApp message', [
            'to' => $toFormatted,
            'from' => $fromFormatted,
        ]);

        try {
            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->withOptions([
                    'verify' => 'C:/laragon/etc/ssl/cacert.pem',
                ])
                ->asForm()
                ->post($url, [
                    'To' => $toFormatted,
                    'From' => $fromFormatted,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                $this->log('info', 'Twilio WhatsApp message sent successfully', [
                    'to' => $to,
                    'message_sid' => $data['sid'] ?? 'unknown',
                    'status' => $data['status'] ?? 'unknown',
                ]);

                return [
                    'success' => true,
                    'message_sid' => $data['sid'] ?? null,
                    'status' => $data['status'] ?? null,
                    'response' => $data,
                ];
            } else {
                $error = $response->json();

                $this->log('error', 'Twilio WhatsApp API error', [
                    'to' => $to,
                    'status' => $response->status(),
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Unknown error',
                    'code' => $error['code'] ?? null,
                    'response' => $error,
                ];
            }
        } catch (\Exception $e) {
            $this->log('error', 'Twilio WhatsApp request exception', [
                'to' => $to,
                'exception' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send a template message (Content Template in Twilio)
     */
    public function sendTemplateMessage(string $to, string $contentSid, array $variables = []): array
    {
        if (!$this->isEnabled()) {
            return [
                'success' => false,
                'error' => 'Twilio WhatsApp is not enabled',
            ];
        }

        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'error' => 'Twilio is not properly configured',
            ];
        }

        $toFormatted = $this->formatPhoneNumber($to);
        $fromFormatted = 'whatsapp:' . $this->fromNumber;

        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->accountSid}/Messages.json";

        $payload = [
            'To' => $toFormatted,
            'From' => $fromFormatted,
            'ContentSid' => $contentSid,
        ];

        // Add content variables if provided
        if (!empty($variables)) {
            $payload['ContentVariables'] = json_encode($variables);
        }

        $this->log('info', 'Sending Twilio WhatsApp template message', [
            'to' => $toFormatted,
            'content_sid' => $contentSid,
            'variables' => $variables,
        ]);

        try {
            $response = Http::withBasicAuth($this->accountSid, $this->authToken)
                ->withOptions([
                    'verify' => 'C:/laragon/etc/ssl/cacert.pem',
                ])
                ->asForm()
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json();

                $this->log('info', 'Twilio WhatsApp template message sent successfully', [
                    'to' => $to,
                    'message_sid' => $data['sid'] ?? 'unknown',
                    'status' => $data['status'] ?? 'unknown',
                ]);

                return [
                    'success' => true,
                    'message_sid' => $data['sid'] ?? null,
                    'status' => $data['status'] ?? null,
                    'response' => $data,
                ];
            } else {
                $error = $response->json();

                $this->log('error', 'Twilio WhatsApp template API error', [
                    'to' => $to,
                    'status' => $response->status(),
                    'error' => $error,
                ]);

                return [
                    'success' => false,
                    'error' => $error['message'] ?? 'Unknown error',
                    'code' => $error['code'] ?? null,
                    'response' => $error,
                ];
            }
        } catch (\Exception $e) {
            $this->log('error', 'Twilio WhatsApp template request exception', [
                'to' => $to,
                'exception' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send ticket created notification
     */
    public function sendTicketCreatedNotification(string $to, string $name, string $ticketNumber, string $department, string $priority): array
    {
        // Check if we have an approved template
        $templateSid = config('twilio.templates.ticket_created');

        if (!empty($templateSid)) {
            // Use approved template
            return $this->sendTemplateMessage($to, $templateSid, [
                '1' => $name,
                '2' => $ticketNumber,
                '3' => $department,
                '4' => $priority,
            ]);
        }

        // Fall back to freeform message (only works within 24-hour window)
        $message = "Hi {$name},\n\n";
        $message .= "Your support ticket has been created successfully!\n\n";
        $message .= "🎫 Ticket Number: {$ticketNumber}\n";
        $message .= "🏢 Department: {$department}\n";
        $message .= "⚡ Priority: {$priority}\n\n";
        $message .= "We'll get back to you shortly.\n\n";
        $message .= "Thank you,\nGolden Knot Holdings";

        return $this->sendMessage($to, $message);
    }

    /**
     * Send ticket updated notification
     */
    public function sendTicketUpdatedNotification(string $to, string $name, string $ticketNumber, string $oldStatus, string $newStatus): array
    {
        // Check if we have an approved template
        $templateSid = config('twilio.templates.ticket_updated');

        if (!empty($templateSid)) {
            // Use approved template
            return $this->sendTemplateMessage($to, $templateSid, [
                '1' => $name,
                '2' => $ticketNumber,
                '3' => $oldStatus,
                '4' => $newStatus,
            ]);
        }

        // Fall back to freeform message (only works within 24-hour window)
        $message = "Hi {$name},\n\n";
        $message .= "Your ticket has been updated.\n\n";
        $message .= "🎫 Ticket Number: {$ticketNumber}\n";
        $message .= "📊 Status: {$oldStatus} → {$newStatus}\n\n";
        $message .= "Thank you,\nGolden Knot Holdings";

        return $this->sendMessage($to, $message);
    }

    /**
     * Send ticket resolved notification
     */
    public function sendTicketResolvedNotification(string $to, string $name, string $ticketNumber): array
    {
        // Check if we have an approved template
        $templateSid = config('twilio.templates.ticket_resolved');

        if (!empty($templateSid)) {
            // Use approved template
            return $this->sendTemplateMessage($to, $templateSid, [
                '1' => $name,
                '2' => $ticketNumber,
            ]);
        }

        // Fall back to freeform message (only works within 24-hour window)
        $message = "Hi {$name},\n\n";
        $message .= "Great news! Your ticket has been resolved. ✅\n\n";
        $message .= "🎫 Ticket Number: {$ticketNumber}\n\n";
        $message .= "If you have any further questions, please don't hesitate to reach out.\n\n";
        $message .= "Thank you,\nGolden Knot Holdings";

        return $this->sendMessage($to, $message);
    }

    /**
     * Send ticket partial closure notification
     */
    public function sendTicketPartialClosedNotification(string $to, string $name, string $ticketNumber, string $completedDepartment, string $pendingDepartment): array
    {
        $message = "Hi {$name},\n\n";
        $message .= "Your ticket is partially closed. ⏳\n\n";
        $message .= "🎫 Ticket Number: {$ticketNumber}\n";
        $message .= "✅ Completed: {$completedDepartment}\n";
        $message .= "⏳ Pending: {$pendingDepartment}\n\n";
        $message .= "The {$completedDepartment} department has completed their work. We are now waiting for the {$pendingDepartment} department to complete their part.\n\n";
        $message .= "We'll notify you once all work is done.\n\n";
        $message .= "Thank you,\nGolden Knot Holdings";

        return $this->sendMessage($to, $message);
    }

    /**
     * Send task assigned notification to agent (immediate notification when task is created)
     */
    public function sendTaskAssignedNotification(string $to, string $agentName, string $taskDescription, string $ticketNumber, string $priority, string $dueDate, string $assignedBy): array
    {
        // Check if we have an approved template
        $templateSid = config('twilio.templates.task_assigned');

        if (!empty($templateSid)) {
            // Use approved template
            return $this->sendTemplateMessage($to, $templateSid, [
                '1' => $agentName,
                '2' => $taskDescription,
                '3' => $ticketNumber,
                '4' => ucfirst($priority),
                '5' => $dueDate,
                '6' => $assignedBy,
            ]);
        }

        // Fall back to freeform message (only works within 24-hour window)
        $priorityEmoji = match($priority) {
            'high' => '🔥',
            'medium' => '⏰',
            default => '📋',
        };

        $message = "Hi {$agentName},\n\n";
        $message .= "📋 New Task Assigned\n\n";
        $message .= "📝 Task: {$taskDescription}\n";
        $message .= "🎫 Ticket: {$ticketNumber}\n";
        $message .= "{$priorityEmoji} Priority: " . ucfirst($priority) . "\n";
        $message .= "📅 Due: {$dueDate}\n";
        $message .= "👤 Assigned by: {$assignedBy}\n\n";
        $message .= "Please complete this task by the due date.\n\n";
        $message .= "Golden Knot TMS";

        return $this->sendMessage($to, $message);
    }

    /**
     * Send task reminder notification to agent
     */
    public function sendTaskReminderNotification(string $to, string $agentName, string $taskDescription, string $ticketNumber, string $priority, bool $isOverdue = false): array
    {
        // Check if we have an approved template
        $templateSid = config('twilio.templates.task_reminder');

        if (!empty($templateSid)) {
            // Use approved template
            return $this->sendTemplateMessage($to, $templateSid, [
                '1' => $agentName,
                '2' => $taskDescription,
                '3' => $ticketNumber,
                '4' => ucfirst($priority),
                '5' => $isOverdue ? 'OVERDUE' : 'Due Now',
            ]);
        }

        // Fall back to freeform message (only works within 24-hour window)
        $priorityEmoji = match($priority) {
            'high' => '🔥',
            'medium' => '⏰',
            default => '📋',
        };

        $overdueText = $isOverdue ? ' ⚠️ OVERDUE' : '';

        $message = "Hi {$agentName},\n\n";
        $message .= "{$priorityEmoji} Task Reminder{$overdueText}\n\n";
        $message .= "📝 Task: {$taskDescription}\n";
        $message .= "🎫 Ticket: {$ticketNumber}\n";
        $message .= "📊 Priority: " . ucfirst($priority) . "\n\n";

        if ($isOverdue) {
            $message .= "⚠️ This task is overdue. Please complete it as soon as possible.\n\n";
        } else {
            $message .= "Please complete this task at your earliest convenience.\n\n";
        }

        $message .= "Golden Knot TMS";

        return $this->sendMessage($to, $message);
    }

    /**
     * Log messages if logging is enabled
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        if ($this->loggingEnabled) {
            Log::$level("[Twilio WhatsApp] {$message}", $context);
        }
    }
}
