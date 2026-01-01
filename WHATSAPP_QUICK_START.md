# WhatsApp Integration - Quick Start Guide

## 🎯 Your Immediate Next Steps

### Step 1: Create Custom Templates (5-10 minutes)
Go to **Meta Business Manager** → **Messaging** → **Templates**

Create these 3 templates:

#### Template 1: gkts_ticket_created
```
Hi {{1}},

Your support ticket has been created successfully!

Ticket Number: {{2}}
Department: {{3}}
Priority: {{4}}

We'll get back to you shortly.

Thank you,
GKTMS Team
```

#### Template 2: gkts_ticket_updated
```
Hi {{1}},

Your ticket has been updated.

Ticket Number: {{2}}
Status Changed: {{3}} → {{4}}

View your ticket: [Link available in application]

Thank you,
GKTMS Team
```

#### Template 3: gkts_ticket_resolved
```
Hi {{1}},

Great news! Your ticket has been resolved.

Ticket Number: {{2}}

If you have any further questions, please don't hesitate to reach out.

Thank you,
GKTMS Team
```

### Step 2: Wait for Approval (24-48 hours per template)
Monitor approval status in Meta Business Manager

### Step 3: Test (15-30 minutes after approval)
1. Login to GKTMS
2. Create a test ticket with your WhatsApp number
3. Verify message received
4. Change ticket status and verify update message
5. Resolve ticket and verify resolved message

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| **WHATSAPP_TEMPLATES_QUICK_REF.md** | 1-page template reference |
| **WHATSAPP_TEMPLATE_SETUP.md** | Detailed setup guide with troubleshooting |
| **WHATSAPP_INTEGRATION_STATUS.md** | Complete system overview |
| **IMPLEMENTATION_CHECKLIST.md** | Phase-by-phase checklist |
| **This File** | Quick start guide |

---

## 🔗 Quick Links

### Meta Business Manager
- URL: https://business.facebook.com
- Section: Messaging → Templates
- Action: Create 3 templates above

### GKTMS Admin Panel
- WhatsApp Settings: `/admin/whatsapp-settings`
- Create Ticket: `/complaints/create`
- Ticket List: `/complaints`
- Audit Logs: `/audit-logs`

### System Logs
- Location: `storage/logs/laravel.log`
- View: Tail file for real-time updates
- Contains: All WhatsApp activity and errors

---

## ✅ Configuration Status

| Item | Status | Details |
|------|--------|---------|
| API Credentials | ✅ Configured | v22.0, Business Account ID, Phone Number ID |
| Phone Number | ✅ Configured | 963214883533253 |
| Access Token | ✅ Valid | Stored securely in `.env` |
| Code Implementation | ✅ Complete | Services, Controllers, Views all ready |
| Template Names | ✅ Set | .env file updated with correct names |
| Phone Formatting | ✅ Working | Auto-converts to 263XXXXXXXXX format |
| Settings UI | ✅ Responsive | Mobile-friendly admin interface |
| Test Connection | ✅ Working | Can verify API connectivity |
| Database Support | ✅ Ready | Phone number field with migrations |
| Notification System | ✅ Ready | Email, In-app, WhatsApp triggers set up |
| Error Logging | ✅ Active | All activity logged to laravel.log |

---

## 🔧 System Components

### Services
- **WhatsAppService** → Sends messages to Meta API
- **NotificationService** → Orchestrates all notifications
- **ActivityLogService** → Tracks audit trail
- **AuditLogService** → Records system activities

### Controllers
- **ComplaintController** → Triggers WhatsApp on creation/update
- **WhatsAppSettingsController** → Admin WhatsApp settings
- **WhatsAppWebhookController** → Receives webhook messages
- **DashboardController** → System overview
- **AuditLogController** → Audit trail viewing

### Models
- **Complaint** → Ticket data with phone_number field
- **User** → Users with WhatsApp fields
- **Notification** → In-app notifications
- **ActivityLog** → Activity tracking
- **AuditLog** → Audit records
- **WhatsAppWebhookLog** → Webhook message logs

---

## 📱 How It Works

### When Customer Creates Ticket
1. User fills ticket form with phone number
2. System auto-formats phone to 263XXXXXXXXX
3. Ticket saved to database
4. Notification system triggered
5. WhatsAppService sends `gkts_ticket_created` template
6. Customer receives WhatsApp message with:
   - Their name
   - Ticket number
   - Department
   - Priority

### When Ticket Status Changes
1. Admin/user updates ticket status
2. ComplaintController detects change
3. NotificationService::sendWhatsAppStatusUpdate() called
4. WhatsAppService sends `gkts_ticket_updated` template
5. Customer receives WhatsApp with old and new status

### When Ticket Resolved
1. Status changed to "Resolved"
2. `gkts_ticket_resolved` template sent
3. Customer notified of resolution

---

## 🚀 Expected Timeline

| Phase | Duration | Action |
|-------|----------|--------|
| Template Creation | 5-10 min | Create 3 templates in Meta |
| Meta Approval | 24-48 hours | Wait for approval (per template) |
| Testing | 15-30 min | Create test tickets, verify messages |
| Deployment | Immediate | System is ready to use |
| Monitoring | Ongoing | Check logs, monitor delivery |

**Total to Go Live:** 2-3 days (mostly waiting for Meta approval)

---

## 📊 What Gets Logged

All WhatsApp activity is logged to `storage/logs/laravel.log`:

```json
{
  "timestamp": "2025-12-20 14:23:45",
  "level": "INFO",
  "message": "[WhatsApp] Ticket created notification sent",
  "data": {
    "ticket_number": "TKT-2025-001",
    "phone": "263717497641",
    "message_id": "wamid.HBgMMjYzNzE3NDk3NjQx..."
  }
}
```

---

## ⚠️ Important Notes

### Phone Number Format
- **Must be:** 263717497641 (11 digits, no + or spaces)
- **Automatically converted** when user enters:
  - +263 71 749 7641
  - 0717497641
  - 263-71-749-7641

### Template Names (Case Sensitive)
- ✅ `gkts_ticket_created`
- ✅ `gkts_ticket_updated`
- ✅ `gkts_ticket_resolved`
- ❌ GktsTicketCreated (wrong case)
- ❌ ticket_created (different name)

### 24-Hour Window Rule
- **Templates:** Can send anytime (approved templates bypass window)
- **Free text messages:** Only within 24 hours of customer message
- **Solution:** We use templates (no window limitation)

---

## 🐛 Troubleshooting Quick Links

| Issue | Solution |
|-------|----------|
| Template not approved | Check Meta for rejection reason, modify, resubmit |
| Message not sending | Verify template approved, check phone number format |
| Error in logs | Read error message, refer to detailed docs |
| Test connection fails | Verify API credentials, check internet connection |
| Settings page not working | Clear cache: `php artisan config:clear` |
| Phone number not formatting | Check Complaint form JavaScript in create.blade.php |

---

## 📞 Support Resources

### Files to Review
1. **WHATSAPP_TEMPLATE_SETUP.md** - Detailed setup guide
2. **WHATSAPP_INTEGRATION_STATUS.md** - System overview
3. **IMPLEMENTATION_CHECKLIST.md** - Testing procedures
4. **storage/logs/laravel.log** - Error logs

### External Resources
- **Meta WhatsApp API:** https://developers.facebook.com/docs/whatsapp
- **Message Templates:** https://www.whatsapp.com/business/api/message-templates/
- **Business Manager:** https://business.facebook.com

---

## ✨ Ready to Go!

The system is **100% configured and ready** for your custom templates.

### Your Checklist:
- [ ] Create 3 templates in Meta Business Manager
- [ ] Wait for approval (usually 24-48 hours)
- [ ] Test with sample ticket
- [ ] Verify message received
- [ ] Monitor logs for errors
- [ ] Go live!

### Questions?
1. Check the detailed docs in this folder
2. Review logs: `storage/logs/laravel.log`
3. Refer to Troubleshooting section in WHATSAPP_TEMPLATE_SETUP.md

---

**Status:** ✅ Ready for Template Creation  
**System:** ✅ Fully Functional  
**Next Action:** Create 3 custom templates in Meta Business Manager  

Good luck! 🚀
