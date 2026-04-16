# ─────────────────────────────────────────────────────────────
# Stage 1 – Node: compile Tailwind / Vite assets
# ─────────────────────────────────────────────────────────────
FROM node:20-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --prefer-offline

# Copy only what's needed for the asset build
COPY resources/ resources/
COPY vite.config.js ./
COPY tailwind.config.js ./
COPY postcss.config.js ./
# Tailwind 4.2 often needs public/ for scanning classes
COPY public/ public/ 

RUN npm run build

# ─────────────────────────────────────────────────────────────
# Stage 2 – PHP: install Composer dependencies
# ─────────────────────────────────────────────────────────────
FROM composer:2.7 AS composer-builder

WORKDIR /app

# Copy dependency files
COPY composer.json composer.lock ./

# Copy the rest of the app (Required so 'artisan' exists for scripts)
COPY . .

# Run install with scripts enabled
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist

# ─────────────────────────────────────────────────────────────
# Stage 3 – Runtime image
# ─────────────────────────────────────────────────────────────
FROM php:8.3-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    mysql-client \
    libpng-dev \
    libjpeg-turbo-dev \
    libwebp-dev \
    freetype-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    curl \
    libintl

# Install PHP extensions
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
 && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        gd \
        zip \
        intl \
        mbstring \
        opcache \
        bcmath \
        pcntl

# Install Redis extension
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps

WORKDIR /var/www/html

# 1. Copy app source
COPY . .

# 2. Copy vendor from composer-builder (already optimized)
COPY --from=composer-builder /app/vendor ./vendor

# 3. Copy compiled assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

# Config files
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/laravel.ini
COPY docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Permissions and Directory Setup
RUN chmod +x /entrypoint.sh artisan \
 && mkdir -p storage/app/public \
             storage/framework/cache \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
             /var/log/supervisor \
             /var/log/nginx \
 && chown -R www-data:www-data /var/www/html \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/entrypoint.sh"]