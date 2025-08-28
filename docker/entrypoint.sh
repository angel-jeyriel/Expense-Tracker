#!/bin/sh
set -e

echo "🚀 Starting Laravel container..."

# Ensure storage & cache are writable
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# Laravel optimizations
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Run migrations (optional – disable if you prefer manual control)
php artisan migrate --force || true

# Start Supervisor (nginx + php-fpm)
exec /usr/bin/supervisord -c /etc/supervisord.conf
