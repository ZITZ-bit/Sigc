#!/bin/sh
set -eu

# ---------------------------------------------------------------------------
# Entrypoint de producción para Laravel SIGC en Render (Docker)
# ---------------------------------------------------------------------------
# 1. Si se provee el contenido del CA de Aiven por variable de entorno, se
#    escribe en la ruta indicada por DB_SSL_CA_PATH (no se incluye en Git ni
#    en la imagen).
# 2. Se sustituye {{PORT}} por el puerto que Render inyecta (variable PORT).
# 3. Se hace cache de configuración/rutas/vistas de Laravel si existe .env y
#    APP_KEY (no se regenera APP_KEY; se asume persistente en Render).
# 4. Se inicia PHP-FPM en segundo plano y Nginx en primer plano (recibe
#    señales de apagado directamente).
# ---------------------------------------------------------------------------

export PORT="${PORT:-8080}"

# Escribir certificado CA de Aiven si se proporciona su contenido
if [ -n "${DB_SSL_CA_CONTENT:-}" ] && [ -n "${DB_SSL_CA_PATH:-}" ]; then
    mkdir -p "$(dirname "${DB_SSL_CA_PATH}")"
    printf '%s' "${DB_SSL_CA_CONTENT}" > "${DB_SSL_CA_PATH}"
    chmod 644 "${DB_SSL_CA_PATH}"
fi

# Asegurar directorios y permisos de escritura en storage y bootstrap/cache
mkdir -p /app/storage/framework/cache /app/storage/framework/sessions /app/storage/framework/views /app/bootstrap/cache /app/storage/logs
chown -R www-data:www-data /app/storage /app/bootstrap/cache 2>/dev/null || true
chmod -R 775 /app/storage /app/bootstrap/cache 2>/dev/null || true

# Cache de configuración de Laravel (solo en producción con .env y APP_KEY)
if [ -f .env ] && [ -n "${APP_KEY:-}" ]; then
    php artisan config:cache --quiet 2>/dev/null || true
    php artisan route:cache --quiet 2>/dev/null || true
    php artisan view:cache --quiet 2>/dev/null || true
fi

# Sustituir puerto en la configuración de Nginx
sed -i "s/{{PORT}}/${PORT}/g" /etc/nginx/conf.d/default.conf

# Iniciar PHP-FPM en segundo plano
php-fpm -D

# Iniciar Nginx en primer plano (recibe señales del orquestador)
exec nginx -g 'daemon off;'
