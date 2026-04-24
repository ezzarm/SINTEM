#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM..."
cd /var/www/html

_DB_HOST="${DB_HOST:-aws-0-ap-southeast-1.pooler.supabase.com}"
_DB_PORT="${DB_PORT:-6543}"
_DB_USER="${DB_USERNAME:-postgres.ehbmbivtxlnjobtpixsw}"
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
LOG_LEVEL="${LOG_LEVEL:-error}"

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

echo "==> [entrypoint] Clearing & warming caches..."
php artisan config:clear 2>/dev/null || true
php artisan route:clear  2>/dev/null || true
php artisan view:clear   2>/dev/null || true
php artisan config:cache 2>/dev/null || true
php artisan route:cache  2>/dev/null || true
php artisan view:cache   2>/dev/null || true

mkdir -p storage/framework/cache/data \
         storage/framework/sessions \
         storage/framework/views \
         storage/logs \
         bootstrap/cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "==> [entrypoint] Bootstrap complete. Starting services..."
exec /usr/bin/supervisord -n -c /etc/supervisor/conf.d/supervisord.conf
