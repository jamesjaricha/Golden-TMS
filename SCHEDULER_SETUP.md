# Task Reminder Scheduler Setup

## Overview
The task reminder system uses Laravel's scheduler to automatically check for due reminders and send notifications every 5 minutes.

## Scheduler Configuration

### Schedule Definition
File: `routes/console.php`
```php
Schedule::command('reminders:check')->everyFiveMinutes();
```

### Command
File: `app/Console/Commands/CheckTicketReminders.php`
- Command: `php artisan reminders:check`
- Runs every 5 minutes via scheduler
- Checks for due reminders (past or current time)
- Sends in-app notifications to assigned users
- Marks reminders as notification_sent = true

## Running the Scheduler

### Option 1: Cron Job (Production - Linux/Mac)
Add to crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### Option 2: Windows Task Scheduler (Production - Windows)
1. Open Task Scheduler
2. Create Basic Task
3. Trigger: Daily at 12:00 AM
4. Action: Start a program
   - Program: `C:\path\to\php.exe`
   - Arguments: `artisan schedule:run`
   - Start in: `C:\path\to\gkts`
5. Edit trigger to repeat every 1 minute for duration of 1 day

### Option 3: Manual Schedule Worker (Development)
Run in terminal (keeps running):
```bash
php artisan schedule:work
```

### Option 4: Manual Check (Testing)
Run once manually:
```bash
php artisan reminders:check
```

## How It Works

1. **Reminder Creation**: When a task reminder is created, `notification_sent` is set to `false`
2. **Scheduler Check**: Every 5 minutes, `reminders:check` command runs
3. **Query Due Reminders**: Finds reminders where:
   - `reminder_datetime` <= now()
   - `status` = 'pending'
   - `notification_sent` = false
4. **Send Notification**: Creates in-app notification via `NotificationService::notifyTaskReminder()`
5. **Mark Sent**: Sets `notification_sent` = true to prevent duplicate notifications
6. **Bell Icon Update**: User sees notification count in topbar bell icon

## Notification Display

### Bell Icon
File: `resources/views/layouts/topbar.blade.php`
- Shows unread count badge
- Dropdown shows recent 5 notifications
- Clicking notification marks as read

### Dashboard Widget
File: `resources/views/dashboard.blade.php`
- Shows pending tasks for logged-in user
- Displays overdue and high priority tasks prominently

## Testing

1. Create a task reminder with due time in the past or within next 5 minutes
2. Run manually: `php artisan reminders:check`
3. Check notification was created: `php artisan tinker`
   ```php
   App\Models\Notification::latest()->first()
   ```
4. Refresh page and check bell icon for notification count

## Troubleshooting

### Notifications Not Showing
1. Verify scheduler is running: `php artisan schedule:list`
2. Check if reminder is due: `reminder_datetime` <= current time
3. Check notification_sent flag: Should be `false` before command runs
4. Run manually: `php artisan reminders:check` and check output
5. Check notifications table: `select * from notifications order by created_at desc limit 5;`

### Duplicate Notifications
- Ensure `notification_sent` column is being updated after sending
- Check TicketReminder model has `notification_sent` in fillable array

## Current Status
✅ Scheduler configured and tested
✅ Command working correctly
✅ Notifications being created
✅ Bell icon displaying notifications
⚠️  **Requires scheduler to be running continuously** (use `schedule:work` in development)
