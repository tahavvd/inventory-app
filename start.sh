#!/bin/sh
set -e

export PORT="${PORT:-80}"

echo "[START] PORT=$PORT"

# Ensure nginx config directory exists
mkdir -p /etc/nginx/sites-enabled

echo "[START] Creating nginx config from template..."
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

echo "[START] Running Laravel migrations..."
cd /var/www/html
php artisan migrate --seed --force 2>&1
echo "[START] Migrations completed"

echo "[START] Clearing Laravel caches..."
php artisan cache:clear 2>&1
php artisan config:clear 2>&1
echo "[START] Caches cleared"

echo "[START] Starting PHP-FPM..."
php-fpm -D
sleep 1
echo "[START] PHP-FPM started"

echo "[START] Starting Nginx on port $PORT..."
nginx -g "daemon off;"

