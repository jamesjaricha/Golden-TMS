<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Complaint;
use App\Models\Department;
use App\Models\User;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Twilio\Rest\Client;

class WhatsAppWizardService
{
    protected ?Client $twilioClient = null;

    /**
     * Process an incoming message and return the response
     */
    public function processMessage(string $phoneNumber, string $message, ?User $agent = null): string
    {
        $conversation = WhatsAppConversation::getOrCreate($phoneNumber, $agent?->id);
        $message = trim($message);
        $messageLower = strtolower($message);

        // Handle global commands
        if (in_array($messageLower, ['cancel', 'stop', 'quit', 'exit'])) {
            $conversation->cancel();
            return $this->getCancelMessage();
        }

        if (in_array($messageLower, ['help', '?'])) {
            return $this->getHelpMessage($agent);
        }

        if (in_array($messageLower, ['back', 'previous', 'prev'])) {
            $conversation->previousStep();
            return $this->getStepPrompt($conversation);
        }

        // Check if starting a new ticket
        if ($this->isStartCommand($messageLower) && !$conversation->isActive()) {
            $conversation->startWizard($agent?->id);
            return $this->getWelcomeMessage($agent) . "\n\n" . $this->getStepPrompt($conversation);
        }

        // If no active conversation and not a start command
        if (!$conversation->isActive()) {
            return $this->getIdleMessage($agent);
        }

        // Process current step
        return $this->processStep($conversation, $message, $agent);
    }

    /**
     * Check if message is a start command
     */
    protected function isStartCommand(string $message): bool
    {
        $startCommands = ['ticket', 'new ticket', 'new', 'start', 'create', 'create ticket', 'hi', 'hello', 'hey'];
        return in_array($message, $startCommands);
    }

    /**
     * Process the current step
     */
    protected function processStep(WhatsAppConversation $conversation, string $input, ?User $agent): string
    {
        $step = $conversation->current_step;

        switch ($step) {
            case 'client_name':
                return $this->handleClientName($conversation, $input);

            case 'client_phone':
                return $this->handleClientPhone($conversation, $input);

            case 'policy_number':
                return $this->handlePolicyNumber($conversation, $input);

            case 'location':
                return $this->handleLocation($conversation, $input);

            case 'branch':
                return $this->handleBranch($conversation, $input);

            case 'department':
                return $this->handleDepartment($conversation, $input);

            case 'issue':
                return $this->handleIssue($conversation, $input);

            case 'priority':
                return $this->handlePriority($conversation, $input);

            case 'confirm':
                return $this->handleConfirmation($conversation, $input, $agent);

            default:
                return $this->getIdleMessage($agent);
        }
    }

    /**
     * Handle client name input
     */
    protected function handleClientName(WhatsAppConversation $conversation, string $input): string
    {
        if (strlen($input) < 2) {
            return "❌ Please enter a valid name (at least 2 characters).\n\n👤 *What is the client's full name?*";
        }

        $conversation->setStepData('client_name', $input);
        $conversation->nextStep();
        return "✅ Client name: *{$input}*\n\n" . $this->getStepPrompt($conversation);
    }

    /**
     * Handle client phone input
     */
    protected function handleClientPhone(WhatsAppConversation $conversation, string $input): string
    {
        // Clean phone number
        $phone = preg_replace('/[^0-9+]/', '', $input);

        if (strlen($phone) < 9) {
            return "❌ Please enter a valid phone number.\n\n📱 *What is the client's phone number?*\n\n_Example: 0771234567_";
        }

        $conversation->setStepData('client_phone', $phone);
        $conversation->nextStep();
        return "✅ Phone: *{$phone}*\n\n" . $this->getStepPrompt($conversation);
    }

    /**
     * Handle policy number input
     */
    protected function handlePolicyNumber(WhatsAppConversation $conversation, string $input): string
    {
        $input = strtoupper(trim($input));

        // Allow "N/A" or "NONE" to skip
        if (in_array($input, ['N/A', 'NA', 'NONE', 'SKIP', '-'])) {
            $input = 'N/A';
        }

        $conversation->setStepData('policy_number', $input);
        $conversation->nextStep();
        return "✅ Policy Number: *{$input}*\n\n" . $this->getStepPrompt($conversation);
    }

    /**
     * Handle location input
     */
    protected function handleLocation(WhatsAppConversation $conversation, string $input): string
    {
        if (strlen($input) < 2) {
            return "❌ Please enter a valid location.\n\n📍 *Where is the client located?*\n\n_Example: Harare CBD, Bulawayo, etc._";
        }

        $conversation->setStepData('location', $input);
        $conversation->nextStep();
        return "✅ Location: *{$input}*\n\n" . $this->getStepPrompt($conversation);
    }

    /**
     * Handle branch selection
     */
    protected function handleBranch(WhatsAppConversation $conversation, string $input): string
    {
        $branches = Branch::orderBy('name')->get();

        // Check if input is a number (selection)
        if (is_numeric($input)) {
            $index = (int)$input - 1;
            if ($index >= 0 && $index < $branches->count()) {
                $branch = $branches[$index];
                $conversation->setStepData('branch_id', $branch->id);
                $conversation->setStepData('branch_name', $branch->name);
                $conversation->nextStep();
                return "✅ Branch: *{$branch->name}*\n\n" . $this->getStepPrompt($conversation);
            }
        }

        // Check if input matches a branch name
        $matchedBranch = $branches->first(function ($branch) use ($input) {
            return stripos($branch->name, $input) !== false;
        });

        if ($matchedBranch) {
            $conversation->setStepData('branch_id', $matchedBranch->id);
            $conversation->setStepData('branch_name', $matchedBranch->name);
            $conversation->nextStep();
            return "✅ Branch: *{$matchedBranch->name}*\n\n" . $this->getStepPrompt($conversation);
        }

        return "❌ Please select a valid branch number or name.\n\n" . $this->getBranchList();
    }

    /**
     * Handle department selection
     */
    protected function handleDepartment(WhatsAppConversation $conversation, string $input): string
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        // Check if input is a number (selection)
        if (is_numeric($input)) {
            $index = (int)$input - 1;
            if ($index >= 0 && $index < $departments->count()) {
                $dept = $departments[$index];
                $conversation->setStepData('department_id', $dept->id);
                $conversation->setStepData('department_name', $dept->name);
                $conversation->nextStep();
                return "✅ Department: *{$dept->name}*\n\n" . $this->getStepPrompt($conversation);
            }
        }

        // Check if input matches a department name
        $matchedDept = $departments->first(function ($dept) use ($input) {
            return stripos($dept->name, $input) !== false;
        });

        if ($matchedDept) {
            $conversation->setStepData('department_id', $matchedDept->id);
            $conversation->setStepData('department_name', $matchedDept->name);
            $conversation->nextStep();
            return "✅ Department: *{$matchedDept->name}*\n\n" . $this->getStepPrompt($conversation);
        }

        return "❌ Please select a valid department number or name.\n\n" . $this->getDepartmentList();
    }

    /**
     * Handle issue description input
     */
    protected function handleIssue(WhatsAppConversation $conversation, string $input): string
    {
        if (strlen($input) < 10) {
            return "❌ Please provide more detail about the issue (at least 10 characters).\n\n📝 *Describe the client's issue/complaint:*";
        }

        $conversation->setStepData('issue_description', $input);
        $conversation->nextStep();
        return "✅ Issue recorded.\n\n" . $this->getStepPrompt($conversation);
    }

    /**
     * Handle priority selection
     */
    protected function handlePriority(WhatsAppConversation $conversation, string $input): string
    {
        $priorities = [
            '1' => 'low',
            '2' => 'medium',
            '3' => 'high',
            '4' => 'urgent',
            'low' => 'low',
            'medium' => 'medium',
            'high' => 'high',
            'urgent' => 'urgent',
        ];

        $inputLower = strtolower(trim($input));

        if (!isset($priorities[$inputLower])) {
            return "❌ Please select a valid priority.\n\n" . $this->getPriorityList();
        }

        $priority = $priorities[$inputLower];
        $conversation->setStepData('priority', $priority);
        $conversation->nextStep();

        $emoji = $this->getPriorityEmoji($priority);
        return "✅ Priority: {$emoji} *" . ucfirst($priority) . "*\n\n" . $this->getStepPrompt($conversation);
    }

    /**
     * Handle confirmation
     */
    protected function handleConfirmation(WhatsAppConversation $conversation, string $input, ?User $agent): string
    {
        $inputLower = strtolower(trim($input));

        if (in_array($inputLower, ['yes', 'y', 'confirm', '1', 'ok', 'correct', 'submit'])) {
            return $this->createTicket($conversation, $agent);
        }

        if (in_array($inputLower, ['no', 'n', 'edit', '2', 'change', 'restart'])) {
            $conversation->startWizard($agent?->id);
            return "🔄 Let's start over.\n\n" . $this->getStepPrompt($conversation);
        }

        return "Please reply:\n*YES* - to create the ticket\n*NO* - to start over\n*CANCEL* - to cancel";
    }

    /**
     * Create the ticket from collected data
     */
    protected function createTicket(WhatsAppConversation $conversation, ?User $agent): string
    {
        try {
            $data = $conversation->collected_data;

            $complaint = Complaint::create([
                'ticket_number' => $this->generateTicketNumber(),
                'full_name' => $data['client_name'],
                'phone_number' => $data['client_phone'],
                'policy_number' => $data['policy_number'] ?? 'N/A',
                'location' => $data['location'],
                'visited_branch' => $data['branch_name'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'department_id' => $data['department_id'] ?? null,
                'complaint_text' => $data['issue_description'],
                'priority' => $data['priority'] ?? 'medium',
                'status' => 'pending',
                'source' => 'whatsapp',
                'captured_by' => $agent?->id ?? $conversation->agent_id,
            ]);

            $conversation->complete($complaint->id);

            // Log activity - pass the agent_id explicitly since we're in webhook context (no Auth::id())
            $agentId = $agent?->id ?? $conversation->agent_id;
            ActivityLogService::log(
                'ticket_created',
                "Ticket {$complaint->ticket_number} created via WhatsApp wizard",
                $complaint,
                ['source' => 'whatsapp_wizard', 'agent_id' => $agentId],
                $agentId  // Pass as 5th parameter to override Auth::id()
            );

            return $this->getSuccessMessage($complaint);

        } catch (\Exception $e) {
            Log::error('[WhatsApp Wizard] Failed to create ticket: ' . $e->getMessage(), [
                'conversation_id' => $conversation->id,
                'data' => $conversation->collected_data,
            ]);
            return "❌ *Error creating ticket*\n\nSomething went wrong. Please try again or contact support.\n\nType *TICKET* to start over.";
        }
    }

    /**
     * Get the prompt for current step
     */
    protected function getStepPrompt(WhatsAppConversation $conversation): string
    {
        switch ($conversation->current_step) {
            case 'client_name':
                return "👤 *Step 1/8: Client Name*\n\nWhat is the client's *full name*?";

            case 'client_phone':
                return "📱 *Step 2/8: Phone Number*\n\nWhat is the client's *phone number*?\n\n_Example: 0771234567_";

            case 'policy_number':
                return "📋 *Step 3/8: Policy Number*\n\nWhat is the client's *policy number*?\n\n_Type N/A if not applicable_";

            case 'location':
                return "📍 *Step 4/8: Location*\n\nWhere is the client *located*?\n\n_Example: Harare, Bulawayo, Mutare_";

            case 'branch':
                return "🏢 *Step 5/8: Branch*\n\nWhich branch did the client visit?\n\n" . $this->getBranchList();

            case 'department':
                return "🏷️ *Step 6/8: Department*\n\nWhich department should handle this?\n\n" . $this->getDepartmentList();

            case 'issue':
                return "📝 *Step 7/8: Issue Description*\n\nDescribe the client's *issue or complaint* in detail:";

            case 'priority':
                return "⚡ *Step 8/8: Priority*\n\nHow urgent is this issue?\n\n" . $this->getPriorityList();

            case 'confirm':
                return $this->getConfirmationSummary($conversation);

            default:
                return $this->getIdleMessage();
        }
    }

    /**
     * Get branch list for selection
     */
    protected function getBranchList(): string
    {
        $branches = Branch::orderBy('name')->get();

        if ($branches->isEmpty()) {
            return "_No branches configured_";
        }

        $list = "";
        foreach ($branches as $index => $branch) {
            $list .= ($index + 1) . ". {$branch->name}\n";
        }
        return $list . "\n_Reply with number or name_";
    }

    /**
     * Get department list for selection
     */
    protected function getDepartmentList(): string
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        if ($departments->isEmpty()) {
            return "_No departments configured_";
        }

        $list = "";
        foreach ($departments as $index => $dept) {
            $list .= ($index + 1) . ". {$dept->name}\n";
        }
        return $list . "\n_Reply with number or name_";
    }

    /**
     * Get priority list for selection
     */
    protected function getPriorityList(): string
    {
        return "1. 🟢 Low\n2. 🟡 Medium\n3. 🟠 High\n4. 🔴 Urgent\n\n_Reply with number or name_";
    }

    /**
     * Get confirmation summary
     */
    protected function getConfirmationSummary(WhatsAppConversation $conversation): string
    {
        $data = $conversation->collected_data;
        $priority = $data['priority'] ?? 'medium';
        $emoji = $this->getPriorityEmoji($priority);

        $summary = "📋 *TICKET SUMMARY*\n";
        $summary .= "━━━━━━━━━━━━━━━━━\n\n";
        $summary .= "👤 *Name:* {$data['client_name']}\n";
        $summary .= "📱 *Phone:* {$data['client_phone']}\n";
        $summary .= "📋 *Policy:* {$data['policy_number']}\n";
        $summary .= "📍 *Location:* {$data['location']}\n";
        $summary .= "🏢 *Branch:* " . ($data['branch_name'] ?? 'N/A') . "\n";
        $summary .= "🏷️ *Department:* " . ($data['department_name'] ?? 'N/A') . "\n";
        $summary .= "{$emoji} *Priority:* " . ucfirst($priority) . "\n\n";
        $summary .= "📝 *Issue:*\n{$data['issue_description']}\n\n";
        $summary .= "━━━━━━━━━━━━━━━━━\n\n";
        $summary .= "Is this correct?\n\n";
        $summary .= "Reply *YES* to create ticket\n";
        $summary .= "Reply *NO* to start over\n";
        $summary .= "Reply *BACK* to edit priority";

        return $summary;
    }

    /**
     * Get success message after ticket creation
     */
    protected function getSuccessMessage(Complaint $complaint): string
    {
        $emoji = $this->getPriorityEmoji($complaint->priority);

        return "✅ *TICKET CREATED SUCCESSFULLY!*\n\n" .
            "🎫 *Ticket #:* {$complaint->ticket_number}\n" .
            "👤 *Client:* {$complaint->full_name}\n" .
            "{$emoji} *Priority:* " . ucfirst($complaint->priority) . "\n" .
            "📊 *Status:* Pending\n\n" .
            "The ticket has been logged and assigned.\n\n" .
            "━━━━━━━━━━━━━━━━━\n" .
            "Type *TICKET* to create another ticket.";
    }

    /**
     * Get welcome message
     */
    protected function getWelcomeMessage(?User $agent): string
    {
        $name = $agent ? $agent->name : 'Agent';
        return "👋 *Hello {$name}!*\n\n" .
            "Let's create a new support ticket.\n" .
            "I'll guide you through each step.\n\n" .
            "━━━━━━━━━━━━━━━━━\n" .
            "💡 *Commands:*\n" .
            "• Type *BACK* to go to previous step\n" .
            "• Type *CANCEL* to cancel anytime\n" .
            "━━━━━━━━━━━━━━━━━";
    }

    /**
     * Get idle message (no active conversation)
     */
    protected function getIdleMessage(?User $agent = null): string
    {
        return "👋 *Golden TMS - Ticket System*\n\n" .
            "To create a new support ticket, type:\n\n" .
            "📝 *TICKET* - Start ticket creation wizard\n" .
            "❓ *HELP* - Show available commands\n\n" .
            "_Your message has been received._";
    }

    /**
     * Get help message
     */
    protected function getHelpMessage(?User $agent): string
    {
        return "❓ *HELP - Available Commands*\n\n" .
            "📝 *TICKET* - Create new ticket (wizard mode)\n" .
            "⬅️ *BACK* - Go to previous step\n" .
            "❌ *CANCEL* - Cancel current ticket\n" .
            "❓ *HELP* - Show this message\n\n" .
            "━━━━━━━━━━━━━━━━━\n\n" .
            "The wizard will guide you through:\n" .
            "1. Client name\n" .
            "2. Phone number\n" .
            "3. Policy number\n" .
            "4. Location\n" .
            "5. Branch visited\n" .
            "6. Department\n" .
            "7. Issue description\n" .
            "8. Priority level\n" .
            "9. Confirmation";
    }

    /**
     * Get cancel message
     */
    protected function getCancelMessage(): string
    {
        return "❌ *Ticket creation cancelled.*\n\n" .
            "Type *TICKET* to start a new ticket.";
    }

    /**
     * Get priority emoji
     */
    protected function getPriorityEmoji(string $priority): string
    {
        return match ($priority) {
            'low' => '🟢',
            'medium' => '🟡',
            'high' => '🟠',
            'urgent' => '🔴',
            default => '🟡',
        };
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
     * Send WhatsApp message via Twilio
     */
    public function sendMessage(string $to, string $message): bool
    {
        if (!config('twilio.enabled')) {
            Log::info('[WhatsApp Wizard] Twilio disabled, message not sent', ['to' => $to]);
            return false;
        }

        try {
            $client = $this->getTwilioClient();
            $from = 'whatsapp:' . config('twilio.whatsapp_from');
            $to = str_starts_with($to, 'whatsapp:') ? $to : 'whatsapp:' . $to;

            $client->messages->create($to, [
                'from' => $from,
                'body' => $message,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('[WhatsApp Wizard] Failed to send message: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get Twilio client
     */
    protected function getTwilioClient(): Client
    {
        if (!$this->twilioClient) {
            $this->twilioClient = new Client(
                config('twilio.account_sid'),
                config('twilio.auth_token')
            );
        }
        return $this->twilioClient;
    }
}
