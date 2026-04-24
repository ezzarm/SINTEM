#!/bin/sh
set -e

echo "==> [entrypoint] Starting SINTEM..."
cd /var/www/html

cat > .env << ENVEOF
APP_NAME="SINTEM"
APP_ENV=production
APP_KEY=${APP_KEY:-base64:mhXwugePA9mU+iaCY/Ezv9urde/1YF5QEzaens//tMw=}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://sintem.up.railway.app}

LOG_CHANNEL=stderr
LOG_LEVEL=${LOG_LEVEL:-error}

DB_CONNECTION=pgsql
DB_HOST=${DB_HOST:-aws-0-ap-southeast-1.pooler.supabase.com}
DB_PORT=${DB_PORT:-6543}
DB_DATABASE=${DB_DATABASE:-postgres}
DB_USERNAME=${DB_USERNAME:-postgres.ehbmbivtxlnjobtpixsw}
DB_PASSWORD=${DB_PASSWORD:-sintempass12}
DB_SSLMODE=require

SUPABASE_URL=${SUPABASE_URL:-https://ehbmbivtxlnjobtpixsw.supabase.co}
SUPABASE_KEY=${SUPABASE_KEY:-eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImVoYm1iaXZ0eGxuam9idHBpeHN3Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3Njk0NjAyOCwiZXhwIjoyMDkyNTIyMDI4fQ.NBsfZmDmXMMPQOidSTu6c_OsQ6BvfNOobmeVY3qRDJQ}
SUPABASE_BUCKET=${SUPABASE_BUCKET:-sintem-files}

SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
TRUSTED_PROXIES=*

CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
BROADCAST_CONNECTION=log
BCRYPT_ROUNDS=12
ENVEOF

echo "==> [entrypoint] .env written. DB_HOST=${DB_HOST:-aws-0-ap-southeast-1.pooler.supabase.com}"

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
