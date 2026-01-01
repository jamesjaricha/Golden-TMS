<?php

namespace App\Http\Controllers;

use App\Services\TwilioWhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwilioSettingsController extends Controller
{
    /**
     * Display the Twilio WhatsApp settings page
     */
    public function index()
    {
        $twilioService = app(TwilioWhatsAppService::class);
        $status = $twilioService->getStatus();

        $settings = [
            'enabled' => config('twilio.enabled'),
            'account_sid' => env('TWILIO_ACCOUNT_SID', ''),
            'auth_token' => env('TWILIO_AUTH_TOKEN', ''),
            'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', ''),
            'sandbox_mode' => config('twilio.sandbox_mode', true),
            'notify_on_create' => config('twilio.notifications.send_on_create', true),
            'notify_on_status_change' => config('twilio.notifications.send_on_status_change', true),
            'notify_on_resolved' => config('twilio.notifications.send_on_resolved', true),
        ];

        return view('settings.twilio', compact('settings', 'status'));
    }

    /**
     * Update Twilio settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'account_sid' => 'required|string|starts_with:AC',
            'auth_token' => 'required|string|min:32',
            'whatsapp_from' => 'required|string|starts_with:+',
        ]);

        // Update .env file
        $this->updateEnvFile([
            'TWILIO_WHATSAPP_ENABLED' => $request->has('enabled') ? 'true' : 'false',
            'TWILIO_ACCOUNT_SID' => $request->account_sid,
            'TWILIO_AUTH_TOKEN' => $request->auth_token,
            'TWILIO_WHATSAPP_FROM' => $request->whatsapp_from,
            'TWILIO_SANDBOX_MODE' => $request->has('sandbox_mode') ? 'true' : 'false',
            'TWILIO_NOTIFY_ON_CREATE' => $request->has('notify_on_create') ? 'true' : 'false',
            'TWILIO_NOTIFY_ON_STATUS_CHANGE' => $request->has('notify_on_status_change') ? 'true' : 'false',
            'TWILIO_NOTIFY_ON_RESOLVED' => $request->has('notify_on_resolved') ? 'true' : 'false',
        ]);

        // Clear config cache
        \Artisan::call('config:clear');

        return redirect()->route('settings.twilio')
            ->with('success', 'Twilio settings updated successfully!');
    }

    /**
     * Test Twilio WhatsApp connection
     */
    public function testConnection(Request $request)
    {
        $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        // Reload config
        \Artisan::call('config:clear');

        $twilioService = new TwilioWhatsAppService();

        if (!$twilioService->isConfigured()) {
            return response()->json([
                'success' => false,
                'message' => 'Twilio is not properly configured. Please check your Account SID, Auth Token, and WhatsApp number.',
            ]);
        }

        $result = $twilioService->sendMessage(
            $request->test_phone,
            "🎉 Hello from Golden Knot TMS!\n\nYour Twilio WhatsApp integration is working correctly.\n\nThis is a test message."
        );

        return response()->json([
            'success' => $result['success'] ?? false,
            'message' => $result['success']
                ? 'Test message sent successfully! Check your WhatsApp.'
                : ($result['error'] ?? 'Failed to send test message.'),
            'details' => $result,
        ]);
    }

    /**
     * Update .env file with new values
     */
    protected function updateEnvFile(array $values): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            // Check if key exists
            if (preg_match("/^{$key}=.*/m", $envContent)) {
                // Update existing key
                $envContent = preg_replace(
                    "/^{$key}=.*/m",
                    "{$key}={$value}",
                    $envContent
                );
            } else {
                // Add new key
                $envContent .= "\n{$key}={$value}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
