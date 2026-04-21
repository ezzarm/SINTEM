#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM deployment..."
cd /var/www/html

# ── 1. Write .env from Railway environment variables ────────
cat > /var/www/html/.env << ENVEOF
APP_NAME="${APP_NAME:-SINTEM}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-sintem}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=${SESSION_ENCRYPT:-false}
# FIX: Default to true in production (Docker/Railway uses HTTPS via proxy).
# The .env file in local dev sets this to false independently.
SESSION_SECURE_COOKIE=${SESSION_SECURE_COOKIE:-true}
SESSION_SAME_SITE=${SESSION_SAME_SITE:-lax}
TRUSTED_PROXIES=*

CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
ENVEOF

echo "==> [entrypoint] .env written."

# ── 2. Generate APP_KEY if not set ──────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY is not set. Generating a temporary one..."
    php artisan key:generate --force
fi

# ── 3. Fix permissions before services start ────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> [entrypoint] Starting supervisor..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf