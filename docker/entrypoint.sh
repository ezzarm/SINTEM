#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM..."
cd /var/www/html

# ─────────────────────────────────────────────────────────────
# 1. Write .env from Railway environment variables
# ─────────────────────────────────────────────────────────────
cat > /var/www/html/.env << ENVEOF
APP_NAME="${APP_NAME:-SINTEM}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL="${LOG_LEVEL:-error}"

DB_CONNECTION=mysql
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-sintem}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=${SESSION_ENCRYPT:-false}
SESSION_SECURE_COOKIE=${SESSION_SECURE_COOKIE:-true}
SESSION_SAME_SITE=${SESSION_SAME_SITE:-lax}
TRUSTED_PROXIES=*

CACHE_STORE=database
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
ENVEOF

echo "==> [entrypoint] .env written."

# ─────────────────────────────────────────────────────────────
# 2. Generate APP_KEY if not set
# ─────────────────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set — generating temporary key."
    php artisan key:generate --force
fi

# ─────────────────────────────────────────────────────────────
# 3. Wait for MySQL
# ─────────────────────────────────────────────────────────────
echo "==> [entrypoint] Waiting for MySQL at ${DB_HOST:-127.0.0.1}..."
max_tries=40
count=0
while ! mysqladmin ping -h"${DB_HOST:-127.0.0.1}" -P"${DB_PORT:-3306}" \
        -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_tries ]; then
        echo "ERROR: MySQL not reachable after $max_tries attempts. Aborting."
        exit 1
    fi
    echo "   Attempt $count/$max_tries — retrying in 3s..."
    sleep 3
done
echo "==> [entrypoint] MySQL is ready."

# ─────────────────────────────────────────────────────────────
# 4. Run migrations
# ─────────────────────────────────────────────────────────────
echo "==> [entrypoint] Running migrations..."
php artisan migrate --force

# ─────────────────────────────────────────────────────────────
# 5. Seed on first deploy only
# ─────────────────────────────────────────────────────────────
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "==> [entrypoint] Seeding initial data..."
    php artisan db:seed --force
else
    echo "==> [entrypoint] Database already seeded — skipping."
fi

# ─────────────────────────────────────────────────────────────
# 6. Ensure upload directories exist (storage is ephemeral on Railway)
# ─────────────────────────────────────────────────────────────
echo "==> [entrypoint] Creating storage directories..."
mkdir -p storage/app/public/upload/photos/lost_found
mkdir -p storage/app/public/upload/photos/event
mkdir -p storage/app/public/upload/photos/announcement
mkdir -p storage/app/public/upload/photos/anonymous_report
mkdir -p storage/app/public/uploads/attachments/announcements
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# ─────────────────────────────────────────────────────────────
# 7. Create storage symlink
# ─────────────────────────────────────────────────────────────
echo "==> [entrypoint] Linking storage..."
# Remove existing public/storage whether it's a dir or symlink
rm -rf public/storage
php artisan storage:link --force

# ─────────────────────────────────────────────────────────────
# 8. Warm up caches (AFTER .env is written and DB is ready)
# ─────────────────────────────────────────────────────────────
echo "==> [entrypoint] Warming caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ─────────────────────────────────────────────────────────────
# 9. Fix permissions
# ─────────────────────────────────────────────────────────────
chown -R www-data:www-data storage bootstrap/cache public/storage
chmod -R 775 storage bootstrap/cache

echo "==> [entrypoint] Bootstrap complete. Starting services..."

# ─────────────────────────────────────────────────────────────
# 10. Start nginx + php-fpm via supervisord
# ─────────────────────────────────────────────────────────────
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
