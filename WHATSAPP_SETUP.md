# WhatsApp Business API Integration Guide

This guide explains how to integrate the Golden Knot Ticket Management System with Meta's WhatsApp Business API for sending ticket notifications to customers.

## Prerequisites

1. A **Meta Business Account** (https://business.facebook.com)
2. A **WhatsApp Business Account** connected to Meta Business
3. A **verified business** (required for production)
4. A **phone number** registered with WhatsApp Business

---

## Step 1: Set Up Meta Business Account

1. Go to [Meta Business Suite](https://business.facebook.com)
2. Create a new Business Account or use an existing one
3. Verify your business (required for production use)

---

## Step 2: Create a Meta App

1. Go to [Meta for Developers](https://developers.facebook.com)
2. Click **"My Apps"** → **"Create App"**
3. Select **"Business"** as the app type
4. Enter your app name and contact email
5. Link your Meta Business Account

---

## Step 3: Set Up WhatsApp Business API

1. In your Meta App dashboard, find **"Add Products"**
2. Click **"Set Up"** on WhatsApp
3. You'll get:
   - **WhatsApp Business Account ID**
   - **Phone Number ID** (after adding a phone number)
   - **Access Token** (generate a permanent one)

### Getting Your Credentials

#### WhatsApp Business Account ID
- Go to WhatsApp → **Getting Started**
- Your Business Account ID is displayed in the API Setup section

#### Phone Number ID
- Go to WhatsApp → **Getting Started** → **API Setup**
- Under "From" dropdown, you'll see your phone number with its ID
- Format: `1234567890123456`

#### Access Token (Permanent)
- Go to Business Settings → **System Users**
- Create a System User with Admin role
- Generate a **Permanent Access Token** with these permissions:
  - `whatsapp_business_messaging`
  - `whatsapp_business_management`

---

## Step 4: Create Message Templates

Message templates must be **approved by Meta** before use. Create these templates in **WhatsApp Manager** → **Message Templates**.

### Template 1: Ticket Created Confirmation

**Name:** `ticket_created_confirmation`
**Category:** Utility
**Language:** English (US)

```
Hello {{1}}, your ticket {{2}} has been created successfully.
Issue: {{3}}
Expected response: {{4}}
Track status: {{5}}
```

**Variables:**
- {{1}} = Client name
- {{2}} = Ticket ID
- {{3}} = Issue category/department
- {{4}} = Expected response time
- {{5}} = Portal link

---

### Template 2: Ticket Status Update

**Name:** `ticket_status_update`
**Category:** Utility
**Language:** English (US)

```
Ticket {{1}} update:
Status: {{2}}
Agent: {{3}}
Message: {{4}}
```

**Variables:**
- {{1}} = Ticket ID
- {{2}} = Status
- {{3}} = Agent name
- {{4}} = Update message

---

### Template 3: Ticket Resolved Notification

**Name:** `ticket_resolved_notification`
**Category:** Utility
**Language:** English (US)

```
Good news {{1}}!
Your ticket {{2}} has been resolved.
Resolution: {{3}}
Reply SATISFIED or UNSATISFIED for feedback.
```

**Variables:**
- {{1}} = Client name
- {{2}} = Ticket ID
- {{3}} = Resolution summary

---

## Step 5: Configure Webhook

The webhook allows WhatsApp to send incoming messages and status updates to your application.

### Webhook URL
```
https://yourdomain.com/webhook/whatsapp
```

### Configure in Meta App Dashboard

1. Go to WhatsApp → **Configuration**
2. Under **Webhook**, click **Edit**
3. Enter:
   - **Callback URL:** `https://yourdomain.com/webhook/whatsapp`
   - **Verify Token:** Your custom token (same as `WHATSAPP_WEBHOOK_VERIFY_TOKEN`)
4. Click **Verify and Save**
5. Subscribe to these webhook fields:
   - `messages`
   - `message_status`

### Webhook Security

Generate a strong random string for your verify token:
```bash
openssl rand -hex 32
```

---

## Step 6: Configure Environment Variables

Add these variables to your `.env` file:

```env
# Enable WhatsApp Integration
WHATSAPP_ENABLED=true
WHATSAPP_API_VERSION=v18.0

# Meta API Credentials
WHATSAPP_BUSINESS_ACCOUNT_ID=your_business_account_id
WHATSAPP_PHONE_NUMBER_ID=your_phone_number_id
WHATSAPP_ACCESS_TOKEN=your_permanent_access_token

# Webhook Security
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_custom_verify_token
WHATSAPP_WEBHOOK_SECRET=your_app_secret_from_meta

# Template Names (must match Meta approved templates)
WHATSAPP_TEMPLATE_TICKET_CREATED=ticket_created_confirmation
WHATSAPP_TEMPLATE_TICKET_UPDATED=ticket_status_update
WHATSAPP_TEMPLATE_TICKET_RESOLVED=ticket_resolved_notification
WHATSAPP_TEMPLATE_LANGUAGE=en_US

# Notification Settings
WHATSAPP_NOTIFY_ON_CREATE=true
WHATSAPP_NOTIFY_ON_STATUS_CHANGE=true
WHATSAPP_NOTIFY_ON_RESOLVED=true
```

---

## Step 7: Test the Integration

### Test Sending a Message

```php
use App\Services\WhatsAppService;

$whatsapp = app(WhatsAppService::class);

// Test with your phone number (must be in international format without +)
$result = $whatsapp->sendTemplateMessage(
    '254712345678', // Kenya phone number format
    'ticket_created_confirmation',
    ['John Doe', 'TKT-000001', 'Billing', '24 hours', 'https://yourapp.com/tickets/1']
);

dd($result);
```

### Test Webhook Verification

```bash
curl "https://yourdomain.com/webhook/whatsapp?hub.mode=subscribe&hub.verify_token=YOUR_TOKEN&hub.challenge=test123"
```

Should return: `test123`

---

## Phone Number Format

WhatsApp requires phone numbers in **international format without the + sign**:

| Country | Format | Example |
|---------|--------|---------|
| Kenya | 254XXXXXXXXX | 254712345678 |
| Nigeria | 234XXXXXXXXX | 2348012345678 |
| South Africa | 27XXXXXXXXX | 27821234567 |
| USA | 1XXXXXXXXXX | 12025551234 |
| UK | 44XXXXXXXXXX | 447911123456 |

---

## Usage in Application

### Automatic Notifications

Notifications are sent automatically when enabled in config:

```php
// In ComplaintController or ComplaintObserver

use App\Services\WhatsAppNotificationService;

// After creating a ticket
$whatsappService = app(WhatsAppNotificationService::class);
$whatsappService->sendTicketCreatedNotification($complaint);

// After status change
$whatsappService->sendStatusUpdateNotification($complaint, 'Your ticket is being processed', $oldStatus);

// After resolution
$whatsappService->sendResolvedNotification($complaint, 'Issue fixed by updating your account settings');
```

### Manual Sending

```php
use App\Services\WhatsAppService;

$whatsapp = app(WhatsAppService::class);

// Send template message
$result = $whatsapp->sendTemplateMessage(
    $phoneNumber,
    $templateName,
    $parameters,
    $language
);

// Check result
if ($result['success']) {
    $messageId = $result['message_id'];
} else {
    $error = $result['error'];
}
```

---

## Rate Limits

Meta WhatsApp Business API has these limits:

| Tier | Messages/24h | Unique Recipients/24h |
|------|-------------|----------------------|
| Tier 1 | 1,000 | 1,000 |
| Tier 2 | 10,000 | 10,000 |
| Tier 3 | 100,000 | 100,000 |
| Tier 4 | Unlimited | Unlimited |

Tier upgrades happen automatically based on quality rating and message volume.

---

## Troubleshooting

### Common Errors

| Error | Solution |
|-------|----------|
| `Template not found` | Ensure template name matches exactly and is approved |
| `Invalid phone number` | Use international format without + |
| `Rate limit exceeded` | Implement queuing and rate limiting |
| `Webhook verification failed` | Check verify token matches exactly |

### Checking Logs

WhatsApp activity is logged to your configured log channel:

```bash
tail -f storage/logs/laravel.log | grep WhatsApp
```

### Webhook Not Receiving

1. Ensure your server is accessible from the internet (HTTPS required)
2. Check webhook is subscribed to correct events
3. Verify webhook URL is correct in Meta dashboard
4. Check webhook logs: `whatsapp_webhook_logs` table

---

## Security Best Practices

1. **Use permanent tokens** from System Users, not short-lived tokens
2. **Verify webhook signatures** using the app secret
3. **Store credentials in `.env`** never in code
4. **Use HTTPS** for webhook endpoint (Meta requires this)
5. **Implement rate limiting** to prevent abuse
6. **Log all API calls** for debugging and audit

---

## Support

- **Meta Business Help Center:** https://www.facebook.com/business/help
- **WhatsApp Business API Docs:** https://developers.facebook.com/docs/whatsapp/cloud-api
- **Meta Developer Support:** https://developers.facebook.com/support

---

## Files Created

| File | Purpose |
|------|---------|
| `config/whatsapp.php` | Configuration settings |
| `app/Services/WhatsAppService.php` | Core API service |
| `app/Services/WhatsAppNotificationService.php` | Notification helper |
| `app/Channels/WhatsAppChannel.php` | Laravel notification channel |
| `app/Notifications/TicketCreatedWhatsApp.php` | Ticket created notification |
| `app/Notifications/TicketStatusUpdatedWhatsApp.php` | Status update notification |
| `app/Notifications/TicketResolvedWhatsApp.php` | Resolution notification |
| `app/Http/Controllers/WhatsAppWebhookController.php` | Webhook handler |
| `app/Models/WhatsAppWebhookLog.php` | Webhook log model |
