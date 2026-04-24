#!/bin/sh
set -e
cd /var/www/html

# ── 1. Wait for MySQL ───────────────────────────────────────
echo "==> [bootstrap] Waiting for MySQL at ${DB_HOST}..."
max_tries=40
count=0
while ! mysqladmin ping -h"$DB_HOST" -P"${DB_PORT:-3306}" -u"$DB_USERNAME" -p"$DB_PASSWORD" --silent 2>/dev/null; do
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

# ── 3. Seed only on first deploy (users table empty) ────────
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "==> [bootstrap] Seeding initial data..."
    php artisan db:seed --force
else
    echo "==> [bootstrap] Database already seeded, skipping."
fi

# ── 4. Optimise caches ──────────────────────────────────────
echo "==> [bootstrap] Optimising caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── 5. Storage link ─────────────────────────────────────────
# Pastikan direktori storage/app/public ada sebelum symlink
mkdir -p /var/www/html/storage/app/public/upload/photos/lost_found
mkdir -p /var/www/html/storage/app/public/upload/photos/event
mkdir -p /var/www/html/storage/app/public/upload/photos/announcement
mkdir -p /var/www/html/storage/app/public/upload/photos/anonymous_report
mkdir -p /var/www/html/storage/app/public/uploads/attachments/announcements
composer require league/flysystem-aws-s3-v3 "^3.0" --with-all-dependencies

echo "==> [bootstrap] Linking storage..."
php artisan storage:link --force 2>/dev/null || true

# ── 6. Fix permissions post-cache ───────────────────────────
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> [bootstrap] SINTEM is ready."
