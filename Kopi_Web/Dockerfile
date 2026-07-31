# 1. Imagen Base Fija: Usamos una versión específica de PHP FPM
FROM php:8.2-fpm

# Directorio de trabajo
WORKDIR /var/www/html

# 2. Dependencias del Sistema Operativo:
# Actualizamos repositorios, instalamos dependencias críticas y limpiamos caché de apt
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# 3. Extensiones Nativas de PHP:
# Instalamos y habilitamos pdo_mysql, mbstring, pcntl, bcmath y gd
RUN docker-php-ext-install pdo_mysql mbstring pcntl bcmath gd

# 4. Ejecución de Composer:
# Obtenemos el binario de Composer versión 2.6
COPY --from=composer:2.6 /usr/bin/composer /usr/bin/composer

# Copiamos primero composer.json y composer.lock para aprovechar el caché de Docker
COPY composer.json composer.lock ./

# Instalamos las dependencias con verbosidad y la ignorando requerimientos de plataforma
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction -v --ignore-platform-reqs

# Copiamos el resto del proyecto
COPY . .

# Ajustamos permisos (típico para aplicaciones como Laravel)
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
