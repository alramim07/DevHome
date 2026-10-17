# ---- Stage 1: build frontend assets ----
FROM node:20-alpine AS assets
WORKDIR /app
ENV PUPPETEER_SKIP_DOWNLOAD=true
COPY package.json package-lock.json* ./
RUN npm ci
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# ---- Stage 2: composer dependencies ----
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# Global helpers are registered in composer.json autoload.files, so make sure
# the file exists in this stage before autoload generation.
COPY app/helpers.php ./app/helpers.php
RUN composer install --no-dev --no-scripts --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# ---- Stage 3: runtime ----
FROM php:8.2-fpm-alpine

# nginx + supervisor + php extensions (pdo_mysql, gd, zip, etc.)
RUN apk add --no-cache nginx supervisor gettext \
    oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev zip unzip linux-headers $PHPIZE_DEPS \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring exif bcmath gd zip opcache \
 && apk del oniguruma-dev libpng-dev libjpeg-turbo-dev freetype-dev $PHPIZE_DEPS

# PHP production tuning
RUN { \
      echo 'memory_limit = 256M'; \
      echo 'upload_max_filesize = 8M'; \
      echo 'post_max_size = 8M'; \
      echo 'opcache.enable = 1'; \
      echo 'opcache.memory_consumption = 128'; \
      echo 'opcache.max_accelerated_files = 20000'; \
    } > /usr/local/etc/php/conf.d/app.ini

# Pass the container environment through to PHP-FPM workers as a safety net
# (the framework config is cached at boot, but this keeps runtime env() working).
RUN printf '[www]\nclear_env = no\n' > /usr/local/etc/php-fpm.d/zzz-env.conf

WORKDIR /var/www/html

# App source
COPY . .
# Composer deps (no dev)
COPY --from=vendor /app/vendor ./vendor
# Built assets (Vite manifest + built css/js)
COPY --from=assets /app/public/build ./public/build

# Storage perms + nginx template + entrypoint
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache \
 && chmod +x docker/entrypoint.sh

# Build the package-discovery cache at image build time
# (composer ran with --no-scripts, which normally triggers package:discover)
RUN php artisan package:discover --ansi || true

COPY docker/default.conf.template /etc/nginx/templates/default.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf

ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
