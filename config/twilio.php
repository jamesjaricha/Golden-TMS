<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Twilio WhatsApp Business API integration.
    | Get your credentials from https://console.twilio.com
    |
    */

    // Enable/Disable Twilio WhatsApp notifications
    'enabled' => env('TWILIO_WHATSAPP_ENABLED', false),

    // Twilio Account SID (from Twilio Console)
    'account_sid' => env('TWILIO_ACCOUNT_SID', ''),

    // Twilio Auth Token (from Twilio Console)
    'auth_token' => env('TWILIO_AUTH_TOKEN', ''),

    // WhatsApp-enabled Twilio phone number (format: +14155238886)
    'whatsapp_from' => env('TWILIO_WHATSAPP_FROM', ''),

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    */

    'notifications' => [
        // Send notification when ticket is created
        'send_on_create' => env('TWILIO_NOTIFY_ON_CREATE', true),

        // Send notification when ticket status changes
        'send_on_status_change' => env('TWILIO_NOTIFY_ON_STATUS_CHANGE', true),

        // Send notification when ticket is assigned
        'send_on_assignment' => env('TWILIO_NOTIFY_ON_ASSIGNMENT', true),

        // Send notification when ticket is resolved
        'send_on_resolved' => env('TWILIO_NOTIFY_ON_RESOLVED', true),

        // Send notification when ticket is escalated
        'send_on_escalated' => env('TWILIO_NOTIFY_ON_ESCALATED', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Templates (Optional)
    |--------------------------------------------------------------------------
    |
    | If you're using Twilio Content Templates, add the Content SIDs here.
    | These are optional - the system can send plain text messages instead.
    |
    */

    'templates' => [
        'ticket_created' => env('TWILIO_TEMPLATE_TICKET_CREATED', ''),
        'ticket_updated' => env('TWILIO_TEMPLATE_TICKET_UPDATED', ''),
        'ticket_resolved' => env('TWILIO_TEMPLATE_TICKET_RESOLVED', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging Settings
    |--------------------------------------------------------------------------
    */

    'logging_enabled' => env('TWILIO_LOGGING_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Sandbox Mode
    |--------------------------------------------------------------------------
    |
    | When using Twilio Sandbox for WhatsApp, recipients must first send
    | a message to your sandbox number to opt-in.
    |
    */

    'sandbox_mode' => env('TWILIO_SANDBOX_MODE', true),

];
