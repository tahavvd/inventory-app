#!/bin/sh
set -e

export PORT="${PORT:-80}"

echo "[START] PORT=$PORT"

# Ensure nginx config directory exists
mkdir -p /etc/nginx/sites-enabled

echo "[START] Creating nginx config from template..."
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

echo "[START] Running Laravel migrations..."
php /var/www/html/artisan migrate --force

echo "[START] Clearing Laravel caches..."
php /var/www/html/artisan cache:clear
php /var/www/html/artisan config:clear

echo "[START] Starting PHP-FPM..."
php-fpm -D
echo "[START] PHP-FPM started"

echo "[START] Starting Nginx on port $PORT..."
nginx -g "daemon off;"

