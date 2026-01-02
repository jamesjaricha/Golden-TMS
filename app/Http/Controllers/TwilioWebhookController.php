<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\User;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\ActivityLogService;
use App\Services\WhatsAppWizardService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Twilio\Security\RequestValidator;

class TwilioWebhookController extends Controller
{
    /**
     * Validate Twilio webhook signature for security
     * Returns true if validation passes or is disabled
     */
    protected function validateTwilioSignature(Request $request): bool
    {
        // Skip validation if explicitly disabled in config
        if (!config('twilio.validate_webhook_signature', true)) {
            Log::info('[Twilio Webhook] Signature validation disabled by config');
            return true;
        }

        // Also skip in local/testing environments
        if (app()->environment('local', 'testing')) {
            Log::info('[Twilio Webhook] Signature validation skipped in development');
            return true;
        }

        $authToken = config('twilio.auth_token');
        if (empty($authToken)) {
            Log::warning('[Twilio Webhook] Auth token not configured, cannot validate signature');
            return false;
        }

        $validator = new RequestValidator($authToken);
        $signature = $request->header('X-Twilio-Signature', '');
        $url = $request->fullUrl();
        $params = $request->all();

        $isValid = $validator->validate($signature, $url, $params);

        if (!$isValid) {
            Log::warning('[Twilio Webhook] Invalid signature detected', [
                'signature' => substr($signature, 0, 20) . '...',
                'url' => $url,
            ]);
        }

        return $isValid;
    }

    /**
     * Handle incoming WhatsApp messages from Twilio
     *
     * Agents can create tickets by:
     * 1. Using the WIZARD command to start a guided conversation
     * 2. Sending WhatsApp messages in direct format:
     *
     * TICKET
     * Client: John Doe
     * Phone: 0771234567
     * Subject: Cannot access account
     * Description: Customer called saying they cannot login to their account since yesterday.
     * Priority: high
     *
     * Or simple format:
     * TICKET Client Name | Phone | Subject | Description | Priority(optional)
     */
    public function handleIncoming(Request $request): Response
    {
        // SECURITY: Validate Twilio signature to prevent forged requests
        if (!$this->validateTwilioSignature($request)) {
            Log::warning('[Twilio Webhook] Request rejected - invalid signature');
            return response('Forbidden', 403);
        }

        Log::info('[Twilio Webhook] Incoming WhatsApp message', $request->all());

        try {
            $messageSid = $request->input('MessageSid') ?? $request->input('SmsMessageSid');
            $from = $request->input('From');
            $body = $request->input('Body', '');
            $profileName = $request->input('ProfileName');

            if (!$messageSid || !$from) {
                Log::warning('[Twilio Webhook] Missing required fields');
                return response('Missing required fields', 400);
            }

            // Check for duplicate
            if (WhatsAppMessage::where('message_sid', $messageSid)->exists()) {
                return response('OK', 200);
            }

            // Clean the phone number (remove whatsapp: prefix)
            $phoneNumber = str_replace('whatsapp:', '', $from);
            $phoneNumber = ltrim($phoneNumber, '+');

            // Check if sender is a registered agent
            $agent = User::where('whatsapp_number', 'LIKE', '%' . substr($phoneNumber, -9))
                ->whereIn('role', ['super_admin', 'manager', 'support_agent'])
                ->first();

            // Store the message
            $message = WhatsAppMessage::create([
                'message_sid' => $messageSid,
                'from_number' => $from,
                'to_number' => $request->input('To') ?? config('twilio.whatsapp_from', 'unknown'),
                'body' => $body,
                'profile_name' => $profileName,
                'media_url' => $request->input('MediaUrl0'),
                'media_type' => $request->input('MediaContentType0'),
                'status' => 'new',
            ]);

            // Check if there's an active wizard conversation for this number
            $wizardService = app(WhatsAppWizardService::class);
            $activeConversation = WhatsAppConversation::where('phone_number', $phoneNumber)
                ->where('status', 'active')
                ->where('expires_at', '>', now())
                ->first();

            // If agent with active conversation, or starting wizard
            if ($agent) {
                // Check if starting new wizard or in active wizard
                if ($activeConversation || $this->isWizardCommand($body)) {
                    return $this->handleWizardFlow($wizardService, $phoneNumber, $body, $agent, $message);
                }

                // Check if it's a direct ticket creation request (legacy format)
                if ($this->isTicketRequest($body)) {
                    return $this->processTicketCreation($message, $agent, $body);
                }

                // Not a ticket or wizard request, send help message
                $this->sendHelpMessage($from, $agent->name);
                return $this->xmlResponse();
            }

            // Unknown sender - store message but don't create ticket
            Log::info('[Twilio Webhook] Message from unregistered number stored', [
                'from' => $from,
                'profile_name' => $profileName,
            ]);

            $this->sendUnknownSenderReply($from);
            return $this->xmlResponse();

        } catch (\Exception $e) {
            Log::error('[Twilio Webhook] Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return response('Error processing message', 500);
        }
    }

    /**
     * Check if message is a wizard start command
     */
    protected function isWizardCommand(string $body): bool
    {
        $body = strtolower(trim($body));
        return in_array($body, ['wizard', 'new', 'start', 'create', 'hi', 'hello', 'help']);
    }

    /**
     * Handle wizard conversation flow
     */
    protected function handleWizardFlow(WhatsAppWizardService $wizardService, string $phoneNumber, string $body, User $agent, WhatsAppMessage $message): Response
    {
        try {
            $result = $wizardService->handleIncomingMessage($phoneNumber, $body, $agent);

            // Update message status if ticket was created
            if (isset($result['ticket'])) {
                $message->update([
                    'status' => 'converted',
                    'complaint_id' => $result['ticket']->id,
                ]);
            }

            return $this->xmlResponse();

        } catch (\Exception $e) {
            Log::error('[Twilio Wizard] Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Send error message to user
            $twilioService = app(\App\Services\TwilioWhatsAppService::class);
            if ($twilioService->isConfigured()) {
                $twilioService->sendMessage($phoneNumber, "❌ Sorry, there was an error. Please try again or type CANCEL to start over.");
            }

            return $this->xmlResponse();
        }
    }

    /**
     * Check if message is a ticket creation request
     */
    protected function isTicketRequest(string $body): bool
    {
        $body = strtoupper(trim($body));
        return str_starts_with($body, 'TICKET') || str_starts_with($body, 'NEW TICKET');
    }

    /**
     * Process ticket creation from WhatsApp message
     */
    protected function processTicketCreation(WhatsAppMessage $message, User $agent, string $body): Response
    {
        try {
            $ticketData = $this->parseTicketData($body);

            if (!$ticketData) {
                $this->sendFormatErrorMessage($message->from_number, $agent->name);
                return $this->xmlResponse();
            }

            // Create the ticket
            $complaint = Complaint::create([
                'ticket_number' => $this->generateTicketNumber(),
                'full_name' => $ticketData['client_name'],
                'phone_number' => $ticketData['client_phone'],
                'email' => $ticketData['client_email'] ?? null,
                'subject' => $ticketData['subject'],
                'description' => $ticketData['description'],
                'priority' => $ticketData['priority'] ?? 'medium',
                'status' => 'pending',
                'source' => 'whatsapp',
                'captured_by' => $agent->id,
                'assigned_to' => $agent->id, // Auto-assign to the creating agent
            ]);

            // Update message status
            $message->update([
                'status' => 'converted',
                'complaint_id' => $complaint->id,
            ]);

            // Log the activity
            ActivityLogService::log(
                'ticket_created',
                "Created ticket {$complaint->ticket_number} via WhatsApp by {$agent->name}",
                $complaint,
                ['source' => 'whatsapp', 'agent_id' => $agent->id]
            );

            // Send confirmation to agent
            $this->sendTicketCreatedConfirmation($message->from_number, $agent->name, $complaint);

            Log::info('[Twilio Webhook] Ticket created via WhatsApp', [
                'ticket_id' => $complaint->id,
                'ticket_number' => $complaint->ticket_number,
                'agent_id' => $agent->id,
            ]);

            return $this->xmlResponse();

        } catch (\Exception $e) {
            Log::error('[Twilio Webhook] Failed to create ticket: ' . $e->getMessage());
            $this->sendErrorMessage($message->from_number);
            return $this->xmlResponse();
        }
    }

    /**
     * Parse ticket data from message body
     *
     * Supports two formats:
     *
     * Format 1 (Detailed):
     * TICKET
     * Client: John Doe
     * Phone: 0771234567
     * Subject: Issue title
     * Description: Detailed description
     * Priority: high
     *
     * Format 2 (Simple - pipe separated):
     * TICKET Client Name | Phone | Subject | Description | Priority(optional)
     */
    protected function parseTicketData(string $body): ?array
    {
        $body = trim($body);

        // Remove "TICKET" or "NEW TICKET" prefix
        $body = preg_replace('/^(NEW\s+)?TICKET\s*/i', '', $body);
        $body = trim($body);

        // Check if it's pipe-separated format
        if (str_contains($body, '|')) {
            return $this->parsePipeFormat($body);
        }

        // Otherwise try line-by-line format
        return $this->parseLineFormat($body);
    }

    /**
     * Parse pipe-separated format:
     * Client Name | Phone | Subject | Description | Priority
     */
    protected function parsePipeFormat(string $body): ?array
    {
        $parts = array_map('trim', explode('|', $body));

        if (count($parts) < 4) {
            return null; // Need at least client, phone, subject, description
        }

        return [
            'client_name' => $parts[0],
            'client_phone' => $this->formatPhoneNumber($parts[1]),
            'subject' => $parts[2],
            'description' => $parts[3],
            'priority' => $this->normalizePriority($parts[4] ?? 'medium'),
        ];
    }

    /**
     * Parse line-by-line format
     */
    protected function parseLineFormat(string $body): ?array
    {
        $data = [];
        $lines = explode("\n", $body);
        $description = [];
        $inDescription = false;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Check for field labels
            if (preg_match('/^(Client|Customer|Name)\s*[:=]\s*(.+)$/i', $line, $matches)) {
                $data['client_name'] = trim($matches[2]);
                $inDescription = false;
            } elseif (preg_match('/^(Phone|Tel|Mobile|Cell)\s*[:=]\s*(.+)$/i', $line, $matches)) {
                $data['client_phone'] = $this->formatPhoneNumber(trim($matches[2]));
                $inDescription = false;
            } elseif (preg_match('/^(Email)\s*[:=]\s*(.+)$/i', $line, $matches)) {
                $data['client_email'] = trim($matches[2]);
                $inDescription = false;
            } elseif (preg_match('/^(Subject|Title|Issue)\s*[:=]\s*(.+)$/i', $line, $matches)) {
                $data['subject'] = trim($matches[2]);
                $inDescription = false;
            } elseif (preg_match('/^(Description|Details|Problem|Query)\s*[:=]\s*(.*)$/i', $line, $matches)) {
                $description[] = trim($matches[2]);
                $inDescription = true;
            } elseif (preg_match('/^(Priority)\s*[:=]\s*(.+)$/i', $line, $matches)) {
                $data['priority'] = $this->normalizePriority(trim($matches[2]));
                $inDescription = false;
            } elseif ($inDescription) {
                // Continue collecting description lines
                $description[] = $line;
            }
        }

        if (!empty($description)) {
            $data['description'] = implode("\n", array_filter($description));
        }

        // Validate required fields
        if (empty($data['client_name']) || empty($data['client_phone']) ||
            empty($data['subject']) || empty($data['description'])) {
            return null;
        }

        return $data;
    }

    /**
     * Format phone number
     */
    protected function formatPhoneNumber(string $phone): string
    {
        // Remove spaces, dashes, etc.
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return $phone;
    }

    /**
     * Normalize priority value
     */
    protected function normalizePriority(string $priority): string
    {
        $priority = strtolower(trim($priority));

        $map = [
            'low' => 'low',
            'l' => 'low',
            '1' => 'low',
            'medium' => 'medium',
            'med' => 'medium',
            'm' => 'medium',
            '2' => 'medium',
            'normal' => 'medium',
            'high' => 'high',
            'h' => 'high',
            '3' => 'high',
            'urgent' => 'urgent',
            'u' => 'urgent',
            'critical' => 'urgent',
            '4' => 'urgent',
        ];

        return $map[$priority] ?? 'medium';
    }

    /**
     * Generate unique ticket number
     */
    protected function generateTicketNumber(): string
    {
        $prefix = 'TKT';
        $date = now()->format('ymd');
        $random = strtoupper(Str::random(4));

        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * Send ticket created confirmation
     */
    protected function sendTicketCreatedConfirmation(string $to, string $agentName, Complaint $complaint): void
    {
        $twilioService = app(\App\Services\TwilioWhatsAppService::class);
        if (!$twilioService->isConfigured()) return;

        $phone = str_replace('whatsapp:', '', $to);

        $message = "✅ *Ticket Created Successfully!*\n\n";
        $message .= "🎫 *Ticket #:* {$complaint->ticket_number}\n";
        $message .= "👤 *Client:* {$complaint->full_name}\n";
        $message .= "📱 *Phone:* {$complaint->phone_number}\n";
        $message .= "📋 *Subject:* {$complaint->subject}\n";
        $message .= "🔴 *Priority:* " . ucfirst($complaint->priority) . "\n";
        $message .= "📊 *Status:* Pending\n\n";
        $message .= "The ticket has been assigned to you.";

        $twilioService->sendMessage($phone, $message);
    }

    /**
     * Send help message showing format
     */
    protected function sendHelpMessage(string $to, string $agentName): void
    {
        $twilioService = app(\App\Services\TwilioWhatsAppService::class);
        if (!$twilioService->isConfigured()) return;

        $phone = str_replace('whatsapp:', '', $to);

        $message = "Hi {$agentName}! 👋\n\n";
        $message .= "*Option 1: Guided Wizard (Recommended)*\n";
        $message .= "Send *WIZARD* or *NEW* to start a step-by-step ticket creation.\n\n";
        $message .= "*Option 2: Quick Format*\n";
        $message .= "Send a message starting with *TICKET* followed by the details:\n";
        $message .= "```\nTICKET Client Name | Phone | Subject | Description\n```\n\n";
        $message .= "*Option 3: Detailed Format*\n";
        $message .= "```\nTICKET\nClient: John Doe\nPhone: 0771234567\nSubject: Issue title\nDescription: Full details here\nPriority: high\n```\n\n";
        $message .= "*Priority options:* low, medium, high, urgent";

        $twilioService->sendMessage($phone, $message);
    }

    /**
     * Send format error message
     */
    protected function sendFormatErrorMessage(string $to, string $agentName): void
    {
        $twilioService = app(\App\Services\TwilioWhatsAppService::class);
        if (!$twilioService->isConfigured()) return;

        $phone = str_replace('whatsapp:', '', $to);

        $message = "❌ *Could not create ticket*\n\n";
        $message .= "Missing required information. Please include:\n";
        $message .= "• Client name\n";
        $message .= "• Phone number\n";
        $message .= "• Subject\n";
        $message .= "• Description\n\n";
        $message .= "*Quick format:*\n";
        $message .= "```\nTICKET Client Name | Phone | Subject | Description\n```\n\n";
        $message .= "Try again with all required fields.";

        $twilioService->sendMessage($phone, $message);
    }

    /**
     * Send error message
     */
    protected function sendErrorMessage(string $to): void
    {
        $twilioService = app(\App\Services\TwilioWhatsAppService::class);
        if (!$twilioService->isConfigured()) return;

        $phone = str_replace('whatsapp:', '', $to);
        $twilioService->sendMessage($phone, "❌ Sorry, there was an error creating the ticket. Please try again or use the web portal.");
    }

    /**
     * Send reply to unknown sender
     */
    protected function sendUnknownSenderReply(string $to): void
    {
        $twilioService = app(\App\Services\TwilioWhatsAppService::class);
        if (!$twilioService->isConfigured()) return;

        $phone = str_replace('whatsapp:', '', $to);
        $message = "Thank you for contacting Golden Knot Holdings.\n\n";
        $message .= "Your phone number is not registered as an agent in our system. ";
        $message .= "If you are an agent, please contact your administrator to register your WhatsApp number.";

        $twilioService->sendMessage($phone, $message);
    }

    /**
     * Handle message status callbacks
     */
    public function handleStatus(Request $request): Response
    {
        // SECURITY: Validate Twilio signature to prevent forged status updates
        if (!$this->validateTwilioSignature($request)) {
            Log::warning('[Twilio Webhook] Status callback rejected - invalid signature');
            return response('Forbidden', 403);
        }

        Log::info('[Twilio Webhook] Status callback', $request->all());
        return response('OK', 200);
    }

    /**
     * Return empty TwiML response
     */
    protected function xmlResponse(): Response
    {
        return response('<?xml version="1.0" encoding="UTF-8"?><Response></Response>', 200)
            ->header('Content-Type', 'text/xml');
    }
}
