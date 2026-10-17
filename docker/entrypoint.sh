#!/bin/sh
set -e

echo "==> DevHome container starting (PORT=${PORT:-10000})"

cd /var/www/html

# --- Database TLS CA certificate -----------------------------------------
# Managed MySQL providers (e.g. Aiven) require TLS. Supply the CA certificate
# either as base64 (DB_SSL_CA_PEM_B64) or raw PEM (DB_SSL_CA_PEM); it is
# written to disk and MYSQL_ATTR_SSL_CA is exported before config is cached.
if [ -n "$DB_SSL_CA_PEM_B64" ]; then
  mkdir -p /etc/ssl/db
  printf '%s' "$DB_SSL_CA_PEM_B64" | base64 -d > /etc/ssl/db/ca.pem
  export MYSQL_ATTR_SSL_CA=/etc/ssl/db/ca.pem
elif [ -n "$DB_SSL_CA_PEM" ]; then
  mkdir -p /etc/ssl/db
  printf '%s\n' "$DB_SSL_CA_PEM" > /etc/ssl/db/ca.pem
  export MYSQL_ATTR_SSL_CA=/etc/ssl/db/ca.pem
fi

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
