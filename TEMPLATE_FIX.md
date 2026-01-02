# WhatsApp Template Category Issue - FIX REQUIRED

## Problem
Error 63049: "Meta chose not to deliver this WhatsApp marketing message"

This means your templates were created with the wrong category.

## Solution
You need to RECREATE all templates in Twilio with the correct category:

### WRONG Category (causes error):
- ‚ùå MARKETING
- ‚ùå CUSTOM

### CORRECT Category (required):
- ‚úÖ UTILITY (use this for all templates)

## Steps to Fix:

1. Go to Twilio Console ‚Üí Messaging ‚Üí Content Template Builder
2. DELETE all existing templates (they have wrong category)
3. Create NEW templates with these settings:

### Template Settings for ALL templates:

**Category:** UTILITY (not TICKET_UPDATE, not MARKETING)
**Language:** English
**Type:** Text

## Templates to Create:

### 1. ticket_created
Category: UTILITY
Body:
```
Hi {{1}},

Your support ticket has been created successfully!

Ìæ´ Ticket Number: {{2}}
Ìø¢ Department: {{3}}
‚ö° Priority: {{4}}

We'll get back to you shortly.

Thank you,
Golden Knot Holdings
```

### 2. ticket_updated  
Category: UTILITY
Body:
```
Hi {{1}},

Your ticket has been updated.

Ìæ´ Ticket Number: {{2}}
Ì≥ä Status: {{3}} ‚Üí {{4}}

Thank you,
Golden Knot Holdings
```

### 3. ticket_resolved
Category: UTILITY
Body:
```
Hi {{1}},

Great news! Your ticket has been resolved. ‚úÖ

Ìæ´ Ticket Number: {{2}}

If you have any further questions, please don't hesitate to reach out.

Thank you,
Golden Knot Holdings
```

### 4. task_assigned
Category: UTILITY
Body:
```
Hi {{1}}, you have been assigned a new task.

Task: {{2}}
Ticket: {{3}}
Priority: {{4}}
Due: {{5}}
Assigned by: {{6}}

Please complete this task by the due date.
```

### 5. task_reminder
Category: UTILITY
Body:
```
Hi {{1}},

‚è∞ Task Reminder

Ì≥ù Task: {{2}}
Ìæ´ Ticket: {{3}}
Ì≥ä Priority: {{4}}
Status: {{5}}

Please complete this task at your earliest convenience.

Golden Knot TMS
```

## Important Notes:

1. **Category MUST be UTILITY** - This tells WhatsApp these are transactional messages
2. After creating with UTILITY category, approval is usually instant or within hours
3. Once approved, update the .env file with new Content SIDs
4. UTILITY templates don't require opt-in and work for service notifications

## Why This Happened:

WhatsApp has 3 template categories:
- **MARKETING** - Promotional content (requires opt-in, rate limited)
- **AUTHENTICATION** - OTP, verification codes
- **UTILITY** - Service notifications, updates, receipts (what you need!)

You likely selected "TICKET_UPDATE" or similar, which Twilio mapped to MARKETING.
