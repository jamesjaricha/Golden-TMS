# WhatsApp Ticket Creation Guide

## Overview
Agents can create tickets directly from their WhatsApp by sending a message to the system's Twilio WhatsApp number. This is useful when agents are in remote areas with limited internet access.

## Setup Requirements

### 1. Register Agent WhatsApp Numbers
Each agent must have their WhatsApp number registered in the system:
- Go to **Users** in the admin panel
- Edit the agent's profile
- Add their WhatsApp number in the `whatsapp_number` field
- Format: Include country code (e.g., `263771234567`)

### 2. Configure Twilio Webhook
In your Twilio Console:
1. Go to **Messaging** → **Settings** → **WhatsApp Sandbox** (or your WhatsApp number)
2. Set the webhook URL:
   ```
   https://your-domain.com/webhook/twilio/incoming
   ```
3. Method: `POST`

---

## Creating Tickets via WhatsApp

### Quick Format (Recommended)
Send a message in this format:
```
TICKET Client Name | Phone Number | Subject | Description
```

**Example:**
```
TICKET Jane Moyo | 0772345678 | Login Problem | Customer cannot access their account since yesterday morning
```

### Quick Format with Priority
```
TICKET Client Name | Phone Number | Subject | Description | Priority
```

**Example:**
```
TICKET John Smith | 0773456789 | Payment Failed | Customer payment was deducted but order not confirmed | high
```

### Detailed Format
For more complex tickets, use the line-by-line format:
```
TICKET
Client: Jane Moyo
Phone: 0772345678
Email: jane@email.com
Subject: Cannot Login to Account
Description: Customer called saying they have been unable to login to their account since yesterday. They have tried resetting their password but still cannot access their account.
Priority: high
```

---

## Priority Levels
| Value | Aliases |
|-------|---------|
| `low` | l, 1 |
| `medium` | med, m, 2, normal |
| `high` | h, 3 |
| `urgent` | u, 4, critical |

If no priority is specified, tickets default to **medium**.

---

## Response Messages

### Success
When a ticket is created successfully, you'll receive:
```
✅ Ticket Created Successfully!

🎫 Ticket #: TKT-260102-A1B2
👤 Client: Jane Moyo
📱 Phone: 0772345678
📋 Subject: Login Problem
🔴 Priority: High
📊 Status: Pending

The ticket has been assigned to you.
```

### Format Error
If required fields are missing:
```
❌ Could not create ticket

Missing required information. Please include:
• Client name
• Phone number
• Subject
• Description

Quick format:
TICKET Client Name | Phone | Subject | Description
```

### Help Message
If you send any message that doesn't start with "TICKET":
```
Hi [Agent Name]! 👋

To create a ticket, send a message starting with TICKET followed by the details.

Quick Format:
TICKET Client Name | Phone | Subject | Description

Detailed Format:
TICKET
Client: John Doe
Phone: 0771234567
Subject: Issue title
Description: Full details here
Priority: high

Priority options: low, medium, high, urgent
```

---

## Tips

1. **Always start with TICKET** - Messages not starting with "TICKET" will show the help message
2. **Use pipes (|) for quick entries** - Faster to type on mobile
3. **Include enough detail** - The description should capture the customer's issue clearly
4. **Phone number format** - Any format works (0772345678, +263772345678, 263772345678)
5. **Tickets auto-assign to you** - You become the owner of tickets you create

---

## Troubleshooting

### "Your phone number is not registered"
Your WhatsApp number is not in the system. Contact your administrator to add your WhatsApp number to your user profile.

### No response received
- Check if the Twilio webhook is configured correctly
- Verify the webhook URL is accessible from the internet
- Check Laravel logs for errors: `storage/logs/laravel.log`

### Ticket not created
- Ensure all required fields are included (client name, phone, subject, description)
- Check that you're using the correct format
- Try the detailed format if the quick format isn't working
