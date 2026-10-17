#!/bin/sh
set -e

echo "==> DevHome container starting (PORT=${PORT:-10000})"

cd /var/www/html

# Ensure writable storage exists
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Public storage symlink (logos, thumbnails, CVs live under public/storage)
if [ ! -e public/storage ]; then
  php artisan storage:link || true
fi

# Generate nginx config from template (only substitutes $PORT)
export PORT="${PORT:-10000}"
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

# Database migrations + framework caches for speed
if [ "$RUN_MIGRATIONS" != "false" ]; then
  echo "==> Running migrations"
  php artisan migrate --force
fi

echo "==> Caching config/routes/views"
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Starting nginx + php-fpm"
exec supervisord -c /etc/supervisord.conf
