#!/bin/sh
set -e

# Resolve target port provided by Render (default 10000)
export PORT="${PORT:-10000}"
echo "==> Configuring Nginx port to $PORT..."

# Substitute PORT in Nginx configuration
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/http.d/default.conf

# Ensure required framework storage and bootstrap directories exist
mkdir -p \
    /var/www/html/storage/app/public \
    /var/www/html/storage/framework/cache/data \
    /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/logs \
    /var/www/html/bootstrap/cache

# Adjust permissions for web user
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure storage symlink exists
if [ ! -L /var/www/html/public/storage ]; then
    echo "==> Creating storage symlink..."
    php artisan storage:link --force || true
fi

# Optimize Laravel route and view caches if APP_KEY is populated
if [ -n "$APP_KEY" ]; then
    echo "==> Warming Laravel route and view caches..."
    php artisan route:cache || true
    php artisan view:cache || true
fi

echo "==> Starting web services via supervisord..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
