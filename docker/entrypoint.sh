#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM deployment..."
cd /var/www/html

# ── 1. Check for APP_KEY ────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. Generating a temporary one..."
    php artisan key:generate --force
fi

# ── 2. Fix permissions before services start ────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> [entrypoint] Starting supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf