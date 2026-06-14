#!/bin/sh
set -e

mkdir -p /var/www/html/lamp_webapp/uploads
chown -R www-data:www-data /var/www/html/lamp_webapp/uploads || true

exec "$@"
