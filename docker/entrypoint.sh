#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM..."
cd /var/www/html

_DB_HOST="${DB_HOST:-db.ehbmbivtxlnjobtpixsw.supabase.co}"
_DB_PORT="${DB_PORT:-5432}"
_DB_USER="${DB_USERNAME:-postgres}"
_DB_PASS="${DB_PASSWORD:-sintempass12}"
_DB_NAME="${DB_DATABASE:-postgres}"
_SUPABASE_URL="${SUPABASE_URL:-https://ehbmbivtxlnjobtpixsw.supabase.co}"
_SUPABASE_KEY="${SUPABASE_KEY:-eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVoYm1iaXZ0eGxuam9idHBpeHN3Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3Njk0NjAyOCwiZXhwIjoyMDkyNTIyMDI4fQ.NBsfZmDmXMMPQOidSTu6c_OsQ6BvfNOobmeVY3qRDJQ}"
_SUPABASE_BUCKET="${SUPABASE_BUCKET:-sintem-files}"

cat > /var/www/html/.env << ENVEOF
APP_NAME="${APP_NAME:-SINTEM}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY:-}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

LOG_CHANNEL=stderr
LOG_LEVEL="${LOG_LEVEL:-debug}"

DB_CONNECTION=pgsql
DB_HOST=${_DB_HOST}
DB_PORT=${_DB_PORT}
DB_DATABASE=${_DB_NAME}
DB_USERNAME=${_DB_USER}
DB_PASSWORD=${_DB_PASS}
DB_SSLMODE=require

SUPABASE_URL=${_SUPABASE_URL}
SUPABASE_KEY=${_SUPABASE_KEY}
SUPABASE_BUCKET=${_SUPABASE_BUCKET}

SESSION_DRIVER=file
SESSION_LIFETIME=${SESSION_LIFETIME:-120}
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
TRUSTED_PROXIES=*

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
ENVEOF

echo "==> [entrypoint] .env written."

if [ -z "$APP_KEY" ]; then
    echo "==> [entrypoint] Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "==> [entrypoint] Waiting for PostgreSQL at ${_DB_HOST}:${_DB_PORT}..."
max_tries=40
count=0
until php -r "
    try {
        \$pdo = new PDO(
            'pgsql:host=${_DB_HOST};port=${_DB_PORT};dbname=${_DB_NAME};sslmode=require',
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
        echo "ERROR: PostgreSQL not reachable after $max_tries attempts. Aborting."
        exit 1
    fi
    echo "   Attempt $count/$max_tries — retrying in 3s..."
    sleep 3
done
echo "==> [entrypoint] PostgreSQL is ready."

echo "==> [entrypoint] Clearing old caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

echo "==> [entrypoint] Warming caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> [entrypoint] Bootstrap complete. Starting services..."

exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
