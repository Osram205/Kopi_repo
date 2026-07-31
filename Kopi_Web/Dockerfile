# Etapa 1: Compilar assets frontend con Node y Vite
FROM node:20.10.0-alpine AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY . .
RUN npm run build

# Etapa 2: Instalar dependencias PHP con Composer
FROM composer:2.6.5 AS vendor-builder
WORKDIR /app

# Instalar dependencias del sistema requeridas por Composer (Solución de exit code: 2)
RUN apk update && apk add --no-cache git unzip zip zlib-dev libzip-dev
RUN docker-php-ext-install zip

COPY composer.json composer.lock ./
# Ignoramos dependencias de desarrollo y scripts para mayor seguridad en la imagen
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .

# Etapa 3: Imagen Final con PHP-FPM
FROM php:8.2.14-fpm-alpine
WORKDIR /var/www/html

# Instalar extensiones necesarias de PHP (pdo_mysql crítico para Kopi_Web)
RUN docker-php-ext-install pdo pdo_mysql

# Copiamos código y dependencias construidas en etapas anteriores
COPY --from=vendor-builder /app /var/www/html
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Permisos para el framework
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
