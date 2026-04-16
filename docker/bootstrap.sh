#!/bin/sh
set -e
cd /var/www/html

# ── 1. Wait for MySQL ───────────────────────────────────────
echo "==> [bootstrap] Waiting for MySQL at ${DB_HOST}..."
max_tries=40
count=0
while ! mysqladmin ping -h"$DB_HOST" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
    count=$((count + 1))
    if [ $count -ge $max_tries ]; then
        echo "ERROR: MySQL not reachable after ${max_tries} attempts. Exiting."
        exit 1
    fi
    echo "   Attempt $count/$max_tries — retrying in 3s..."
    sleep 3
done
echo "==> [bootstrap] MySQL is ready."

# ── 2. Run migrations ───────────────────────────────────────
echo "==> [bootstrap] Running migrations..."
php artisan migrate --force

# ── 3. Optimise caches ─────────────────────────────────────
echo "==> [bootstrap] Optimising caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan icons:cache 2>/dev/null || echo "No icons to cache"

# ── 4. Storage link ─────────────────────────────────────────
echo "==> [bootstrap] Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# ── 5. Fix permissions post-cache ───────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> [bootstrap] SINTEM is ready."