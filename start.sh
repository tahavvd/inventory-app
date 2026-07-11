#!/bin/sh
set -e

export PORT="${PORT:-80}"
mkdir -p /etc/nginx/sites-enabled
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

php-fpm -D
nginx -g "daemon off;"