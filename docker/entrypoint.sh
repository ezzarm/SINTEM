#!/bin/sh
set -e

echo "==> [entrypoint] Starting Sintem deployment..."

cd /var/www/html

# ── 1. Write .env from Railway environment variables ─────────
cat > .env << ENVEOF
APP_NAME="${APP_NAME:-SINTEM}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=database
SESSION_LIFETIME=120

CACHE_STORE=database
QUEUE_CONNECTION=sync

FILESYSTEM_DISK=local
ENVEOF

# ── 2. Generate app key if not set ──────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "==> [entrypoint] Generating APP_KEY..."
    php artisan key:generate --force
fi

# ── 3. Wait for MySQL to be ready ───────────────────────────
echo "==> [entrypoint] Waiting for MySQL at ${DB_HOST}:${DB_PORT:-3306}..."
max_tries=30
count=0
until mysqladmin ping -h"${DB_HOST}" -P"${DB_PORT:-3306}" -u"${DB_USERNAME}" -p"${DB_PASSWORD}" --silent 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_tries ]; then
        echo "ERROR: MySQL not reachable after ${max_tries} attempts. Exiting."
        exit 1
    fi
    echo "   Attempt $count/$max_tries — retrying in 3s..."
    sleep 3
done
echo "==> [entrypoint] MySQL is ready."

# ── 4. Run migrations ───────────────────────────────────────
echo "==> [entrypoint] Running migrations..."
php artisan migrate --force

# ── 5. Clear & warm caches ──────────────────────────────────
echo "==> [entrypoint] Optimising caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 6. Storage link ─────────────────────────────────────────
if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# ── 7. Fix permissions ──────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> [entrypoint] All done. Starting supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf