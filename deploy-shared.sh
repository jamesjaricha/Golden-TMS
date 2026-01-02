#!/bin/bash
#=============================================================================
# Golden TMS - Shared Hosting Quick Deploy Script
#=============================================================================
# Run this on your shared hosting server after uploading files
# Usage: bash deploy-shared.sh
#=============================================================================

set -e

echo "🚀 Golden TMS - Shared Hosting Deployment"
echo "=========================================="

# Check if we're in the right directory
if [ ! -f "artisan" ]; then
    echo "❌ Error: Run this script from the golden-tms directory"
    exit 1
fi

# Step 1: Check/Create .env
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        echo "📝 Creating .env from .env.example..."
        cp .env.example .env
        echo "⚠️  IMPORTANT: Edit .env with your database credentials!"
        echo "   Run: nano .env"
        echo ""
        echo "Then run this script again."
        exit 0
    else
        echo "❌ Error: .env.example not found"
        exit 1
    fi
fi

# Step 2: Check for APP_KEY
if grep -q "APP_KEY=$" .env || grep -q "APP_KEY=\"\"" .env; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
fi

# Step 3: Install Composer dependencies
echo ""
echo "📦 Installing PHP dependencies..."
if command -v composer &> /dev/null; then
    composer install --no-dev --optimize-autoloader --no-interaction
elif [ -f "composer.phar" ]; then
    php composer.phar install --no-dev --optimize-autoloader --no-interaction
else
    echo "⚠️  Composer not found. Downloading..."
    curl -sS https://getcomposer.org/installer | php
    php composer.phar install --no-dev --optimize-autoloader --no-interaction
fi

# Step 4: Set permissions
echo ""
echo "🔐 Setting permissions..."
chmod -R 775 storage 2>/dev/null || true
chmod -R 775 bootstrap/cache 2>/dev/null || true
chmod 600 .env 2>/dev/null || true

# Step 5: Run migrations
echo ""
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Step 6: Clear and optimize
echo ""
echo "⚡ Optimizing application..."
php artisan optimize:clear
php artisan optimize

# Step 7: Storage link
echo ""
echo "🔗 Creating storage link..."
php artisan storage:link 2>/dev/null || echo "   Storage link may already exist"

echo ""
echo "=========================================="
echo "✅ Deployment Complete!"
echo "=========================================="
echo ""
echo "📋 Next steps:"
echo "   1. Make sure public/ is your document root"
echo "   2. Set up SSL certificate"
echo "   3. Configure cron job for scheduler:"
echo "      * * * * * cd $(pwd) && php artisan schedule:run >> /dev/null 2>&1"
echo ""
echo "🔗 Test your site at your domain!"
echo ""
