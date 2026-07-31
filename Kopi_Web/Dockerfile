FROM php:8.4-apache
WORKDIR /var/www/html

# Instalar extensiones necesarias de PHP
RUN apt-get update && apt-get install -y libzip-dev zip unzip \
    && docker-php-ext-install pdo pdo_mysql zip

# Habilitar el módulo mod_rewrite de Apache para Laravel
RUN a2enmod rewrite

# CONFIGURACIÓN DE APACHE (Puerto 8080 y DocumentRoot a /public)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# 1. Cambiar el puerto de escucha global de Apache a 8080
RUN sed -i 's/Listen 80/Listen 8080/' /etc/apache2/ports.conf

# 2. INYECTAR VIRTUALHOST: Bloque exacto con permisos de lectura, symlinks y rewrite
RUN echo "<VirtualHost *:8080>\n\
    DocumentRoot ${APACHE_DOCUMENT_ROOT}\n\
    <Directory ${APACHE_DOCUMENT_ROOT}>\n\
        Options Indexes FollowSymLinks\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Copiamos código y dependencias construidas en etapas anteriores
COPY --from=vendor-builder /app /var/www/html
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# Crear carpetas obligatorias del framework por si Git las ignoró
RUN mkdir -p /var/www/html/storage/framework/sessions \
    /var/www/html/storage/framework/views \
    /var/www/html/storage/framework/cache

# Permisos correctos para que Laravel pueda escribir
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Exponer el puerto configurado
EXPOSE 8080

# Iniciar Apache en primer plano
CMD ["apache2-foreground"]