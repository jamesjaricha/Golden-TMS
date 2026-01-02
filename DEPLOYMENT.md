# 🚀 Golden TMS - Production Deployment Guide

This guide covers deploying Golden TMS to a production server.

---

## 📋 Pre-Deployment Checklist

### ✅ Before You Start
- [ ] Domain name configured and pointing to server
- [ ] SSL certificate ready (Let's Encrypt recommended)
- [ ] Server meets requirements (PHP 8.2+, Composer, Node.js 18+)
- [ ] Database server ready (MySQL/PostgreSQL recommended for production)
- [ ] SMTP credentials for email notifications
- [ ] Twilio credentials (if using WhatsApp)

---

## 🖥️ Server Requirements

| Requirement | Minimum | Recommended |
|-------------|---------|-------------|
| PHP | 8.2 | 8.3 |
| Memory | 512MB | 1GB+ |
| Storage | 1GB | 5GB+ |
| Database | SQLite | MySQL 8.0+ / PostgreSQL 14+ |

### Required PHP Extensions
```
- BCMath
- Ctype
- cURL
- DOM
- Fileinfo
- JSON
- Mbstring
- OpenSSL
- PDO (+ pdo_mysql or pdo_pgsql)
- Tokenizer
- XML
- Zip
```

---

## 📦 Step-by-Step Deployment

### Step 1: Upload Files to Server

```bash
# Option A: Using Git (Recommended)
cd /var/www
git clone https://github.com/yourusername/golden-tms.git
cd golden-tms

# Option B: Upload via SFTP/FTP
# Upload all files EXCEPT: node_modules/, vendor/, .env, storage/logs/*
```

### Step 2: Install Dependencies

```bash
# Install PHP dependencies (production mode)
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm ci
npm run build
```

### Step 3: Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Edit .env with your production values
nano .env
```

**Critical `.env` settings for production:**
```env
APP_NAME="Golden TMS"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database (example for MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=golden_tms
DB_USERNAME=your_user
DB_PASSWORD=your_secure_password

# Session Security
SESSION_SECURE_COOKIE=true
SESSION_ENCRYPT=true

# Logging
LOG_LEVEL=error

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.yourprovider.com
# ... configure all mail settings

# Twilio (if using)
TWILIO_VALIDATE_WEBHOOK_SIGNATURE=true
TWILIO_SANDBOX_MODE=false
```

### Step 4: Set Up Database

```bash
# For MySQL/PostgreSQL: Create database first
mysql -u root -p -e "CREATE DATABASE golden_tms CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate --force

# (Optional) Seed initial data
php artisan db:seed --force
```

### Step 5: Set Directory Permissions

```bash
# Set ownership (adjust www-data to your web server user)
sudo chown -R www-data:www-data /var/www/golden-tms

# Set directory permissions
sudo chmod -R 755 /var/www/golden-tms
sudo chmod -R 775 /var/www/golden-tms/storage
sudo chmod -R 775 /var/www/golden-tms/bootstrap/cache
```

### Step 6: Optimize for Production

```bash
# Clear any existing caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### Step 7: Configure Web Server

#### Nginx Configuration (Recommended)
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com;
    root /var/www/golden-tms/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256;
    ssl_prefer_server_ciphers off;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    index index.php;
    charset utf-8;

    # Gzip Compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache Configuration (.htaccess is included)
Ensure `mod_rewrite` is enabled:
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

### Step 8: Set Up Queue Worker (Optional but Recommended)

Create a systemd service for queue processing:

```bash
sudo nano /etc/systemd/system/golden-tms-queue.service
```

```ini
[Unit]
Description=Golden TMS Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
RestartSec=3
WorkingDirectory=/var/www/golden-tms
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl enable golden-tms-queue
sudo systemctl start golden-tms-queue
```

### Step 9: Set Up Scheduler (Cron)

```bash
sudo crontab -e -u www-data
```

Add this line:
```
* * * * * cd /var/www/golden-tms && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🔒 Security Hardening

### 1. File Permissions
```bash
# Ensure .env is not accessible
chmod 600 .env

# Ensure storage directory is protected
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 2. Hide Sensitive Files
Add to your web server config to block access to:
- `.env`
- `.git/`
- `composer.json`
- `composer.lock`
- `package.json`

### 3. Enable HTTPS Only
Ensure `SESSION_SECURE_COOKIE=true` in `.env`

### 4. Firewall Rules
```bash
# Allow only necessary ports
sudo ufw allow 22/tcp    # SSH
sudo ufw allow 80/tcp    # HTTP (redirects to HTTPS)
sudo ufw allow 443/tcp   # HTTPS
sudo ufw enable
```

---

## 🔄 Post-Deployment

### Verify Installation
```bash
# Check Laravel version
php artisan --version

# Check routes are cached
php artisan route:list --compact

# Check if storage is linked
ls -la public/storage

# Test database connection
php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connected!';"
```

### Create Admin User (if needed)
```bash
php artisan tinker
```
```php
\App\Models\User::create([
    'name' => 'Admin',
    'email' => 'admin@your-domain.com',
    'password' => bcrypt('your-secure-password'),
    'role' => 'admin',
]);
```

### Test the Application
1. Visit https://your-domain.com
2. Log in with admin credentials
3. Test creating a ticket
4. Test WhatsApp notifications (if configured)
5. Check the dashboard analytics

---

## 🔧 Maintenance Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Re-optimize after changes
php artisan optimize

# Put in maintenance mode
php artisan down --secret="your-secret-bypass-key"

# Bring back up
php artisan up

# View logs
tail -f storage/logs/laravel.log

# Run pending migrations
php artisan migrate --force
```

---

## 📊 Monitoring & Logs

### Log Locations
- **Application Logs:** `storage/logs/laravel.log`
- **Nginx Logs:** `/var/log/nginx/access.log` & `error.log`
- **PHP-FPM Logs:** `/var/log/php8.2-fpm.log`

### Recommended Monitoring
- **Uptime:** UptimeRobot, Pingdom
- **Errors:** Laravel Telescope (dev), Sentry (production)
- **Performance:** New Relic, Laravel Debugbar (dev only)

---

## 🆘 Troubleshooting

### 500 Internal Server Error
```bash
# Check Laravel logs
tail -100 storage/logs/laravel.log

# Check permissions
ls -la storage/
ls -la bootstrap/cache/

# Clear caches
php artisan cache:clear
php artisan config:clear
```

### Database Connection Issues
```bash
# Test connection
php artisan tinker --execute="DB::connection()->getPdo();"

# Check .env database settings
cat .env | grep DB_
```

### Session/Login Issues
```bash
# Ensure sessions table exists
php artisan session:table
php artisan migrate

# Clear session data
php artisan cache:clear
```

### WhatsApp Not Working
1. Verify Twilio credentials in `.env`
2. Check `TWILIO_WHATSAPP_ENABLED=true`
3. Verify webhook URL is accessible: `https://your-domain.com/webhooks/twilio/incoming`
4. Check logs for Twilio errors

---

## 📞 Support Contacts

- **Developer:** [Your Contact Info]
- **Hosting Provider:** [Provider Support]
- **Twilio Support:** https://support.twilio.com

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0.0 | 2026-01-02 | Initial production release |

---

**Last Updated:** January 2, 2026
