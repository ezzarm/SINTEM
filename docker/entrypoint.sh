#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM deployment..."

# Ensure we are in the right directory
cd /var/www/html

# ── 1. Check for APP_KEY ────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. Generating a temporary one..."
    php artisan key:generate --force
fi

# ── 2. Wait for MySQL to be ready ───────────────────────────
# Using a simpler check that doesn't strictly require mysqladmin if preferred, 
# but mysqladmin is fine since we installed it.
echo "==> [entrypoint] Waiting for MySQL at ${DB_HOST}..."
max_tries=30
count=0
while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent; do
    count=$((count + 1))
    if [ $count -ge $max_tries ]; then
        echo "ERROR: MySQL not reachable after ${max_tries} attempts. Exiting."
        exit 1
    fi
    echo "   Attempt $count/$max_tries — retrying in 3s..."
    sleep 3
done
echo "==> [entrypoint] MySQL is ready."

# ── 3. Run migrations ───────────────────────────────────────
echo "==> [entrypoint] Running migrations..."
# --force is required in production
php artisan migrate --force

# ── 4. Optimise for Laravel 13 ──────────────────────────────
echo "==> [entrypoint] Optimising caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
# Laravel 13/Vite optimization
php artisan icons:cache 2>/dev/null || echo "No icons to cache"

# ── 5. Storage link ─────────────────────────────────────────
echo "==> [entrypoint] Linking storage..."
php artisan storage:link --force

# ── 6. Final Permission Check ───────────────────────────────
# Ensure the webserver can write to storage even if files were created by root
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> [entrypoint] SINTEM is ready. Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf