# Email Testing Setup for GKTMS

## ✅ Changes Made

1. **Notification Badge Fixed**
   - Changed from `bg-red-500` to `bg-red-600` with `shadow-lg` for better visibility
   - Badge now has better contrast and shadow to stand out

2. **Email Configuration Updated**
   - Changed `MAIL_MAILER` from `log` to `smtp`
   - Changed `MAIL_PORT` from `2525` to `1025` (for MailHog/Mailpit)
   - Added `MAIL_ENCRYPTION=null` for local testing
   - Changed `MAIL_FROM_ADDRESS` to `noreply@gktms.local`

## 📧 Email Testing Options for Laragon

### Option 1: MailHog (Recommended - Easy Setup)

MailHog is a simple email testing tool that catches all outgoing emails.

**Installation:**
1. Download MailHog from: https://github.com/mailhog/MailHog/releases
2. Extract `MailHog.exe` to `C:\laragon\bin\mailhog\` (create the folder)
3. Run MailHog by double-clicking `MailHog.exe`
4. Access the web interface at: http://localhost:8025

**Your .env is already configured for MailHog:**
```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_ENCRYPTION=null
```

**Start MailHog:**
- Open Command Prompt or PowerShell
- Navigate to `C:\laragon\bin\mailhog\`
- Run: `MailHog.exe`
- Keep it running in the background
- View emails at: http://localhost:8025

### Option 2: Mailtrap (Cloud-Based Testing)

Mailtrap is a cloud service specifically for email testing.

**Setup:**
1. Sign up at: https://mailtrap.io (free tier available)
2. Create an inbox
3. Get your SMTP credentials from the inbox settings
4. Update your `.env` file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@gktms.local"
MAIL_FROM_NAME="GKTMS"
```

5. Run: `php artisan config:clear`
6. Check emails at: https://mailtrap.io/inboxes

### Option 3: Gmail SMTP (For Production-Like Testing)

**⚠️ Warning:** Only use this if you want to send real emails for testing.

**Setup:**
1. Enable 2-Factor Authentication on your Gmail account
2. Generate an App Password: https://myaccount.google.com/apppasswords
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="your_email@gmail.com"
MAIL_FROM_NAME="GKTMS"
```

4. Run: `php artisan config:clear`

## 🧪 Testing Email Notifications

After setting up your chosen email testing method:

1. **Create a New Ticket:**
   - Login as admin
   - Go to Complaints → Create New
   - Fill in the form
   - **Assign the ticket to a user** (e.g., Tendai)
   - Submit

2. **Check for Email:**
   - **MailHog:** Visit http://localhost:8025
   - **Mailtrap:** Check your inbox at mailtrap.io
   - **Gmail:** Check the recipient's inbox

3. **Email Should Include:**
   - ✅ Subject: "New Ticket Assigned to You"
   - ✅ Ticket number and details
   - ✅ Client name, policy, department
   - ✅ Priority badge (color-coded)
   - ✅ Complaint preview
   - ✅ "View Ticket" button
   - ✅ CC to all managers/super admins

4. **Check In-App Notification:**
   - Login as the assigned user
   - Look for the red badge on the bell icon (should now be visible!)
   - Click the bell to see the notification
   - Click the notification to view the ticket (marks as read)

## 🔍 Troubleshooting

### Emails Still Not Sending?

1. **Check Laravel Logs:**
   ```bash
   cd d:/laragon/www/GKTMS
   cat storage/logs/laravel.log
   ```

2. **Test Email Configuration:**
   ```bash
   php artisan tinker
   Mail::raw('Test email', function($msg) {
       $msg->to('test@example.com')->subject('Test');
   });
   exit
   ```

3. **Verify MailHog is Running:**
   - Open http://localhost:8025 in your browser
   - You should see the MailHog interface
   - If not, MailHog isn't running

4. **Clear Config Again:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan queue:restart  # If using queues
   ```

### Badge Still Not Visible?

1. **Clear Browser Cache:** Ctrl + F5
2. **Check Console for CSS Errors:** F12 → Console tab
3. **Verify Tailwind Classes:** The badge should have `bg-red-600 text-white shadow-lg`

## 📋 Current Configuration Summary

**File: `.env`**
- `MAIL_MAILER=smtp` (changed from `log`)
- `MAIL_HOST=127.0.0.1`
- `MAIL_PORT=1025` (MailHog port)
- `MAIL_ENCRYPTION=null` (for local testing)
- `MAIL_FROM_ADDRESS="noreply@gktms.local"`

**Notification Badge:**
- Location: `resources/views/layouts/navigation.blade.php` (line ~59)
- Classes: `bg-red-600 text-white shadow-lg rounded-full`
- Shows count with "9+" for values over 9

**Email Template:**
- File: `resources/views/emails/ticket-assigned.blade.php`
- Design: Apple-inspired with gradient header
- Includes: Ticket details, priority badge, "View Ticket" button

## 🚀 Quick Start (MailHog - Easiest)

1. Download MailHog: https://github.com/mailhog/MailHog/releases/download/v1.0.1/MailHog_windows_amd64.exe
2. Rename to `MailHog.exe` and place in `C:\laragon\bin\mailhog\`
3. Double-click `MailHog.exe` to start
4. Visit http://localhost:8025 to see the email viewer
5. Assign a ticket in GKTMS
6. Check MailHog - email should appear instantly!

## 📞 Support

If emails still don't work after trying MailHog:
1. Check if port 1025 is available: `netstat -an | grep 1025`
2. Try changing `MAIL_PORT` to `1026` in both `.env` and MailHog startup
3. Check Windows Firewall settings
4. Review Laravel logs: `storage/logs/laravel.log`

---

**Last Updated:** December 20, 2025  
**System:** GKTMS v1.0 on Laragon with Laravel 12.43.1
