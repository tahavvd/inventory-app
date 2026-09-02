#!/bin/sh
set -e

export PORT="${PORT:-80}"

echo "[START] PORT=$PORT"

# Ensure nginx config directory exists
mkdir -p /etc/nginx/sites-enabled

echo "[START] Creating nginx config from template..."
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

cd /var/www/html

echo "[START] Starting PHP-FPM..."
php-fpm -D
sleep 1
echo "[START] PHP-FPM started"

echo "[START] Starting Nginx on port $PORT..."
nginx -g "daemon off;"

