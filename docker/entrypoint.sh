#!/bin/sh
set -eu

port="${PORT:-80}"
sed -i "s/^Listen 80$/Listen ${port}/" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost *:${port}>/" /etc/apache2/sites-available/000-default.conf

mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ ! -L public/storage ]; then
    php artisan storage:link
fi

# Ejecutar migraciones si la BD está disponible
if [ "${DB_CONNECTION:-}" = "postgres" ] || [ "${DB_CONNECTION:-}" = "mysql" ]; then
    echo "Ejecutando migraciones..."
    php artisan migrate --force --ansi || echo "Advertencia: Las migraciones fallaron, continuando..."
fi

exec "$@"