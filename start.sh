#!/bin/sh
set -e

export PORT="${PORT:-80}"

echo "[START] PORT=$PORT"

# Ensure nginx config directory exists
mkdir -p /etc/nginx/sites-enabled

echo "[START] Creating nginx config from template..."
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default
echo "[START] Nginx config created:"
cat /etc/nginx/sites-enabled/default

echo "[START] Starting PHP-FPM..."
php-fpm -D
echo "[START] PHP-FPM started (PID: $(pgrep php-fpm))"

echo "[START] Starting Nginx..."
nginx -g "daemon off;"

