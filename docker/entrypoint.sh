#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM..."
cd /var/www/html

_DB_HOST="${MYSQLHOST:-${DB_HOST:-127.0.0.1}}"
_DB_PORT="${MYSQLPORT:-${DB_PORT:-3306}}"
_DB_USER="${MYSQLUSER:-${MYSQLUSER:-${DB_USERNAME:-root}}}"
_DB_PASS="${MYSQLPASSWORD:-${DB_PASSWORD:-}}"
_DB_NAME="${MYSQLDATABASE:-${DB_DATABASE:-sintem}}"

cat > /var/www/html/.env << ENVEOF
APP_NAME="${APP_NAME:-SINTEM}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-true}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL="${LOG_LEVEL:-debug}"

DB_CONNECTION=mysql
DB_HOST=${_DB_HOST}
DB_PORT=${_DB_PORT}
DB_DATABASE=${_DB_NAME}
DB_USERNAME=${_DB_USER}
DB_PASSWORD=${_DB_PASS}

SESSION_DRIVER=${SESSION_DRIVER:-file}
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=${SESSION_ENCRYPT:-false}
SESSION_SECURE_COOKIE=${SESSION_SECURE_COOKIE:-true}
SESSION_SAME_SITE=${SESSION_SAME_SITE:-lax}
TRUSTED_PROXIES=*

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
ENVEOF

echo "==> [entrypoint] .env written."

if [ -z "$APP_KEY" ]; then
    echo "WARNING: APP_KEY not set — generating temporary key."
    php artisan key:generate --force
fi

echo "==> [entrypoint] Waiting for MySQL at ${_DB_HOST}:${_DB_PORT}..."
max_tries=40
count=0
until php -r "
    try {
        \$pdo = new PDO(
            'mysql:host=${_DB_HOST};port=${_DB_PORT};dbname=${_DB_NAME}',
            '${_DB_USER}',
            '${_DB_PASS}',
            [PDO::ATTR_TIMEOUT => 5, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        exit(0);
    } catch (Exception \$e) {
        exit(1);
    }
" 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_tries ]; then
        echo "ERROR: MySQL not reachable after $max_tries attempts. Aborting."
        exit 1
    fi
    echo "   Attempt $count/$max_tries — retrying in 3s..."
    sleep 3
done
echo "==> [entrypoint] MySQL is ready."

echo "==> [entrypoint] Running migrations..."
php artisan migrate --force 2>&1

USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "==> [entrypoint] Seeding initial data..."
    php artisan db:seed --force
else
    echo "==> [entrypoint] Database already seeded — skipping."
fi

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

echo "==> [entrypoint] Linking storage..."
rm -rf public/storage
php artisan storage:link --force

echo "==> [entrypoint] Clearing old caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "==> [entrypoint] Warming caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache public/storage
chmod -R 775 storage bootstrap/cache

echo "==> [entrypoint] Bootstrap complete. Starting services..."

exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf