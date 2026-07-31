# Etapa 1: Compilar assets frontend con Node moderno
FROM node:22-alpine AS frontend-builder
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm ci
COPY . .
RUN npm run build

# Etapa 2: Instalar dependencias PHP con Composer
FROM composer:2.6.5 AS vendor-builder
WORKDIR /app
RUN apk update && apk add --no-cache git unzip zip zlib-dev libzip-dev
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .

# Etapa 3: Imagen Final con Servidor Web Apache integrado
FROM php:8.3-apache
WORKDIR /var/www/html

# Instalar extensiones necesarias de PHP
RUN apt-get update && apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilitar el módulo mod_rewrite de Apache para Laravel
RUN a2enmod rewrite

# CONFIGURACIÓN DE APACHE (Puerto 8080 y DocumentRoot a /public)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# 1. Cambiar el puerto de escucha de Apache a 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# 2. Actualizar el VirtualHost por defecto para usar el puerto 8080 y apuntar a /public
RUN sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:8080>/' /etc/apache2/sites-enabled/000-default.conf
RUN sed -i "s|DocumentRoot /var/www/html|DocumentRoot ${APACHE_DOCUMENT_ROOT}|" /etc/apache2/sites-enabled/000-default.conf

# 3. Dar permisos de acceso al nuevo DocumentRoot en la configuración global de Apache
RUN sed -i "s|/var/www/|${APACHE_DOCUMENT_ROOT}|g" /etc/apache2/apache2.conf

# Copiamos código y dependencias construidas en etapas anteriores
COPY --from=vendor-builder /app /var/www/html
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Permisos correctos para las carpetas de almacenamiento y caché de Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto configurado
EXPOSE 8080

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]