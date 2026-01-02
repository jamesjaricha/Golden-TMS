# WhatsApp Message Templates for Twilio

This document contains all WhatsApp message templates needed for the Golden Knot TMS system. Create these templates in **Twilio Console → Messaging → Content Template Builder** and submit them for WhatsApp approval.

## Template 1: Ticket Created
**Template Name:** `ticket_created`  
**Category:** TICKET_UPDATE  
**Language:** English

**Variables:**
- {{1}} = Customer Name
- {{2}} = Ticket Number
- {{3}} = Department
- {{4}} = Priority

**Body:**
```
Hi {{1}},

Your support ticket has been created successfully!

🎫 Ticket Number: {{2}}
🏢 Department: {{3}}
⚡ Priority: {{4}}

We'll get back to you shortly.

Thank you,
Golden Knot Holdings
```

---

## Template 2: Ticket Updated
**Template Name:** `ticket_updated`  
**Category:** TICKET_UPDATE  
**Language:** English

**Variables:**
- {{1}} = Customer Name
- {{2}} = Ticket Number
- {{3}} = Old Status
- {{4}} = New Status

**Body:**
```
Hi {{1}},

Your ticket has been updated.

🎫 Ticket Number: {{2}}
📊 Status: {{3}} → {{4}}

Thank you,
Golden Knot Holdings
```

---

## Template 3: Ticket Resolved
**Template Name:** `ticket_resolved`  
**Category:** TICKET_UPDATE  
**Language:** English

**Variables:**
- {{1}} = Customer Name
- {{2}} = Ticket Number

**Body:**
```
Hi {{1}},

Great news! Your ticket has been resolved. ✅

🎫 Ticket Number: {{2}}

If you have any further questions, please don't hesitate to reach out.

Thank you,
Golden Knot Holdings
```

---

## Template 4: Ticket Partial Closed
**Template Name:** `ticket_partial_closed`  
**Category:** TICKET_UPDATE  
**Language:** English

**Variables:**
- {{1}} = Customer Name
- {{2}} = Ticket Number
- {{3}} = Completed Department
- {{4}} = Pending Department

**Body:**
```
Hi {{1}},

Your ticket is partially closed. ⏳

🎫 Ticket Number: {{2}}
✅ Completed: {{3}}
⏳ Pending: {{4}}

The {{3}} department has completed their work. We are now waiting for the {{4}} department to complete their part.

We'll notify you once all work is done.

Thank you,
Golden Knot Holdings
```

---

## Template 5: Task Assigned (Agent Notification)
**Template Name:** `task_assigned`  
**Category:** TICKET_UPDATE  
**Language:** English

**Variables:**
- {{1}} = Agent Name
- {{2}} = Task Description
- {{3}} = Ticket Number
- {{4}} = Priority
- {{5}} = Due Date
- {{6}} = Assigned By

**Body:**
```
Hi {{1}}, you have been assigned a new task.

Task: {{2}}
Ticket: {{3}}
Priority: {{4}}
Due: {{5}}
Assigned by: {{6}}

Please complete this task by the due date.
```

**✅ Already Created:** `HX986f3c9b53e60166e7531d33f485855e`

---

## Template 6: Task Reminder (Agent Notification)
**Template Name:** `task_reminder`  
**Category:** TICKET_UPDATE  
**Language:** English

**Variables:**
- {{1}} = Agent Name
- {{2}} = Task Description
- {{3}} = Ticket Number
- {{4}} = Priority
- {{5}} = Status (OVERDUE or Due Now)

**Body:**
```
Hi {{1}},

⏰ Task Reminder

📝 Task: {{2}}
🎫 Ticket: {{3}}
📊 Priority: {{4}}
Status: {{5}}

Please complete this task at your earliest convenience.

Golden Knot TMS
```

---

## Configuration

After creating these templates in Twilio and getting WhatsApp approval, add the Content SIDs to your `.env` file:

```env
# Twilio Content Template SIDs
TWILIO_TEMPLATE_TICKET_CREATED=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TEMPLATE_TICKET_UPDATED=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TEMPLATE_TICKET_RESOLVED=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TEMPLATE_TASK_ASSIGNED=HX986f3c9b53e60166e7531d33f485855e
TWILIO_TEMPLATE_TASK_REMINDER=HXxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Note:** Template for `ticket_partial_closed` is not currently configured in the system but can be added if needed.

---

## Approval Timeline

WhatsApp typically takes **24-48 hours** to review and approve message templates. Make sure your templates:
- Don't contain promotional content
- Are transactional in nature
- Don't violate WhatsApp's commerce policy
- Use proper formatting

---

## Testing

Once templates are approved, test them using:

```bash
php artisan tinker
```

```php
$twilioService = app(App\Services\TwilioWhatsAppService::class);

// Test ticket created
$result = $twilioService->sendTicketCreatedNotification(
    '263771430442',
    'James Jaricha',
    'TKT-20260102-0001A',
    'IT Support',
    'High'
);

// Test task assigned
$result = $twilioService->sendTaskAssignedNotification(
    '263771430442',
    'James Jaricha',
    'Follow up with customer',
    'TKT-20260102-0001A',
    'high',
    'Jan 03, 2026 03:00 PM',
    'Admin User'
);
```
