# syntax=docker/dockerfile:1
# ---------------------------------------------------------------------------
# Dockerfile de producción — Laravel SIGC (Render + Docker)
# Multi-stage: composer → node (vite build) → runtime (PHP-FPM + Nginx)
# ---------------------------------------------------------------------------

# ===========================================================================
# Etapa 1: Dependencias de PHP (Composer)
# ===========================================================================
FROM composer:2.10 AS composer
WORKDIR /app

# Solo se copian los manifiestos para aprovechar caché de Docker
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# ===========================================================================
# Etapa 2: Build del frontend (Vite + Tailwind)
# ===========================================================================
FROM node:26 AS node
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js ./
COPY resources ./resources

RUN npm run build

# ===========================================================================
# Etapa 3: Runtime — PHP-FPM + Nginx
# ===========================================================================
FROM php:8.5-fpm

WORKDIR /app

# Dependencias del sistema y extensiones PHP requeridas por Laravel
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        nginx \
        curl \
        ca-certificates \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        libonig-dev \
        unzip \
        zip \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuración PHP de producción
COPY docker/php.ini /usr/local/etc/php/conf.d/app.ini

# Dependencias de Composer desde la etapa anterior
COPY --from=composer /app/vendor ./vendor

# Build del frontend desde la etapa anterior
COPY --from=node /app/public/build ./public/build

# Código fuente de la aplicación (excluidos por .dockerignore: .env, vendor,
# node_modules, public/build, etc.)
COPY . .

# Configuración de Nginx
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf

# Punto de entrada
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Permisos de escritura para www-data (PHP-FPM y Nginx)
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

EXPOSE 8080

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
