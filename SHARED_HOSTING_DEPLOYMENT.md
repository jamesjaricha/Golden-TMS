# 🚀 Golden TMS - Shared Hosting Deployment Guide

This guide is for deploying to shared hosting (like cPanel, Plesk, or similar) with SSH access.

---

## 📋 Pre-Deployment Checklist

- [ ] SSH access to your hosting
- [ ] Domain pointing to your hosting
- [ ] SSL certificate (most hosts provide free Let's Encrypt)
- [ ] Know your document root path (usually `public_html` or `www`)
- [ ] Database credentials (MySQL usually provided by host)

---

## 🚀 Quick Deployment Steps

### Step 1: Connect to Your Server

```bash
ssh your-username@your-server.com
```

### Step 2: Navigate to Web Directory

```bash
# Common paths:
cd ~/public_html
# or
cd ~/www
# or
cd ~/domains/your-domain.com/public_html
```

### Step 3: Clone or Upload the Project

**Option A: Git Clone (Recommended)**
```bash
# Clone into a temporary folder first
git clone https://github.com/jamesjaricha/Golden-TMS.git golden-tms-temp

# Move files to correct location (see structure below)
```

**Option B: Upload via SFTP**
- Upload all files using FileZilla or similar
- Exclude: `node_modules/`, `vendor/`, `.env`

### Step 4: Correct Directory Structure

⚠️ **IMPORTANT**: Laravel's `public` folder must be your document root.

**Recommended Structure:**
```
/home/username/
├── golden-tms/              ← Laravel app (OUTSIDE public_html)
│   ├── app/
│   ├── bootstrap/
│   ├── config/
│   ├── database/
│   ├── resources/
│   ├── routes/
│   ├── storage/
│   ├── vendor/
│   ├── .env
│   └── ...
│
└── public_html/             ← Document root (symlink or copy public/)
    ├── index.php            ← Modified to point to ../golden-tms
    ├── build/
    ├── css/
    ├── js/
    └── ...
```

**Set this up:**
```bash
# Move Laravel app outside public_html
cd ~
mv public_html/golden-tms-temp ./golden-tms

# Backup and clear public_html
mv public_html public_html_backup

# Create symlink from Laravel's public to public_html
ln -s ~/golden-tms/public ~/public_html

# OR if symlinks don't work, copy public folder:
cp -r ~/golden-tms/public ~/public_html
```

### Step 5: Update index.php Paths (if you copied public folder)

If you copied instead of symlinked, edit `public_html/index.php`:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../golden-tms/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../golden-tms/vendor/autoload.php';

// Bootstrap Laravel and handle the request...
(require_once __DIR__.'/../golden-tms/bootstrap/app.php')
    ->handleRequest(Request::capture());
```

### Step 6: Install Dependencies

```bash
cd ~/golden-tms

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# If npm is available, build assets (or build locally and upload)
npm ci && npm run build
```

**If npm is NOT available on your host:**
Build locally on your computer first, then upload the `public/build/` folder.

### Step 7: Configure Environment

```bash
cd ~/golden-tms

# Create .env file
cp .env.example .env

# Generate app key
php artisan key:generate

# Edit .env with your settings
nano .env
```

**Essential .env settings:**
```env
APP_NAME="Golden TMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# MySQL Database (get from your hosting panel)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

# Email (configure with your host's SMTP or external service)
MAIL_MAILER=smtp
MAIL_HOST=mail.your-domain.com
MAIL_PORT=587
MAIL_USERNAME=noreply@your-domain.com
MAIL_PASSWORD=your_email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@your-domain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Step 8: Set Up Database

```bash
cd ~/golden-tms

# Run migrations
php artisan migrate --force

# (Optional) Create admin user
php artisan tinker --execute="App\Models\User::create(['name'=>'Admin','email'=>'admin@your-domain.com','password'=>bcrypt('YourSecurePassword'),'role'=>'admin']);"
```

### Step 9: Set Permissions

```bash
cd ~/golden-tms

# Make storage and cache writable
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Secure .env file
chmod 600 .env
```

### Step 10: Optimize for Production

```bash
cd ~/golden-tms

# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Create storage link
php artisan storage:link
```

### Step 11: Set Up Cron Job (for Scheduler)

Add via cPanel/Plesk Cron Jobs or SSH:

```bash
crontab -e
```

Add this line:
```
* * * * * cd ~/golden-tms && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔧 Common Shared Hosting Issues & Fixes

### Issue: "500 Internal Server Error"
```bash
# Check Laravel logs
tail -50 ~/golden-tms/storage/logs/laravel.log

# Check permissions
chmod -R 775 ~/golden-tms/storage
chmod -R 775 ~/golden-tms/bootstrap/cache
```

### Issue: "Composer not found"
```bash
# Download Composer locally
curl -sS https://getcomposer.org/installer | php
php composer.phar install --no-dev --optimize-autoloader
```

### Issue: "PHP version too low"
Check if your host allows selecting PHP version in cPanel. You need PHP 8.2+.

Or create `.htaccess` to force PHP version:
```apache
# In public_html/.htaccess
AddHandler application/x-httpd-php82 .php
```

### Issue: "npm not available"
Build locally on your computer:
```bash
npm ci
npm run build
```
Then upload the `public/build/` folder to your server.

### Issue: Symlinks not working
Copy public folder instead:
```bash
cp -r ~/golden-tms/public/* ~/public_html/
```
Then update `public_html/index.php` to point to `../golden-tms/` paths.

### Issue: Storage link not working
Create manually:
```bash
cd ~/public_html
ln -s ../golden-tms/storage/app/public storage
```

Or in cPanel File Manager, create a symbolic link.

---

## 📁 File Upload Checklist (if not using Git)

Upload these folders/files:
- ✅ `app/`
- ✅ `bootstrap/`
- ✅ `config/`
- ✅ `database/`
- ✅ `public/` (to public_html or as symlink)
- ✅ `resources/`
- ✅ `routes/`
- ✅ `storage/`
- ✅ `artisan`
- ✅ `composer.json`
- ✅ `composer.lock`
- ✅ `.env.example`

**DO NOT upload:**
- ❌ `vendor/` (install via composer on server)
- ❌ `node_modules/` (not needed if you upload build/)
- ❌ `.env` (create on server)
- ❌ `.git/`

---

## ✅ Post-Deployment Verification

1. **Visit your domain** - Should see login page
2. **Check health endpoint** - `https://your-domain.com/health.php`
3. **Try logging in** - Use the admin account you created
4. **Create a test ticket** - Verify database is working
5. **Check logs** - `tail -f ~/golden-tms/storage/logs/laravel.log`

---

## 🔄 Future Updates

When you need to update the application:

```bash
cd ~/golden-tms

# Pull latest changes (if using Git)
git pull origin main

# Install any new dependencies
composer install --no-dev --optimize-autoloader

# Run new migrations
php artisan migrate --force

# Clear and rebuild caches
php artisan optimize:clear
php artisan optimize
```

---

## 📞 Quick Commands Reference

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize

# Check application status
php artisan about

# View recent logs
tail -100 storage/logs/laravel.log

# Maintenance mode on
php artisan down --secret="your-bypass-key"

# Maintenance mode off
php artisan up
```

---

**Need help?** Check your hosting provider's Laravel documentation or contact their support.
