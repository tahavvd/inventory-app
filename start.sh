#!/bin/sh
set -e

export PORT="${PORT:-80}"
envsubst '${PORT}' < /etc/nginx/templates/default.conf.template > /etc/nginx/sites-enabled/default

php-fpm -D
nginx -g "daemon off;"