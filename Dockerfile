# ===============================================================
# Stage 1: Build Frontend Assets (Vue 3, TypeScript, Vite)
# ===============================================================
FROM node:22-alpine AS frontend-builder
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --ignore-scripts

COPY tsconfig.json vite.config.js postcss.config.js ./
COPY resources ./resources

RUN npm run build

# ===============================================================
# Stage 2: Install Composer Dependencies
# ===============================================================
FROM composer:2 AS composer-builder
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-reqs

# ===============================================================
# Stage 3: Production Runtime (PHP 8.3-FPM + Nginx on Alpine)
# ===============================================================
FROM php:8.3-fpm-alpine

# Install system runtime dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    gettext \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip

# Configure and compile PHP extensions required by Laravel 13 & MySQL
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        bcmath \
        opcache \
        pcntl \
        exif \
        zip \
        gd

# Copy Composer binary for package discovery
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy configuration files
COPY docker/nginx.conf /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/php.ini /usr/local/etc/php/conf.d/production.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p /etc/nginx/http.d /var/log/supervisor /run/nginx

WORKDIR /var/www/html

# Copy application source
COPY --chown=www-data:www-data . /var/www/html

# Copy pre-built vendor from Stage 2
COPY --from=composer-builder --chown=www-data:www-data /app/vendor /var/www/html/vendor

# Copy compiled frontend assets from Stage 1
COPY --from=frontend-builder --chown=www-data:www-data /app/public/build /var/www/html/public/build

# Discover Laravel packages
RUN php artisan package:discover --ansi

# Setup storage and cache directories permissions
RUN mkdir -p \
        /var/www/html/storage/app/public \
        /var/www/html/storage/framework/cache/data \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/logs \
        /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Render default port
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
