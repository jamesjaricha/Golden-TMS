#!/bin/bash
#=============================================================================
# Golden TMS - Production Deployment Script
#=============================================================================
# Usage: bash deploy.sh
# Run this script on the production server after pulling changes
#=============================================================================

set -e  # Exit on any error

echo "🚀 Starting Golden TMS Deployment..."
echo "================================================"

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: artisan file not found. Please run this script from the project root."
    exit 1
fi

# Check if .env exists
if [ ! -f ".env" ]; then
    echo "❌ Error: .env file not found. Please copy .env.example to .env and configure it."
    exit 1
fi

echo ""
echo "📦 Step 1: Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo ""
echo "📦 Step 2: Installing npm dependencies..."
npm ci --production=false

echo ""
echo "🔨 Step 3: Building frontend assets..."
npm run build

echo ""
echo "🗄️ Step 4: Running database migrations..."
php artisan migrate --force

echo ""
echo "🧹 Step 5: Clearing old caches..."
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo ""
echo "⚡ Step 6: Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

echo ""
echo "🔗 Step 7: Ensuring storage link exists..."
php artisan storage:link 2>/dev/null || echo "Storage link already exists"

echo ""
echo "🔐 Step 8: Setting permissions..."
if [ -d "storage" ]; then
    chmod -R 775 storage
fi
if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache
fi

echo ""
echo "================================================"
echo "✅ Deployment completed successfully!"
echo "================================================"
echo ""
echo "📋 Post-deployment checklist:"
echo "   □ Verify the site is accessible"
echo "   □ Test login functionality"
echo "   □ Check error logs: tail -f storage/logs/laravel.log"
echo "   □ Restart queue workers if running"
echo ""
