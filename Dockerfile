# Establecemos la imagen base de PHP con Apache
FROM php:8.3-apache

# Instalamos dependencias del sistema
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    unzip \
    git \
    postgresql-client \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql zip

# Habilitamos el módulo mod_rewrite de Apache (necesario para Laravel)
RUN a2enmod rewrite

# Instalamos Composer (dependencias de PHP)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Establecemos el directorio de trabajo
WORKDIR /var/www/html

# Copiamos los archivos del proyecto Laravel al contenedor
COPY . .

# Instalamos las dependencias de PHP con Composer
RUN composer install --optimize-autoloader --no-dev

# Configuración para Apache
COPY ./docker/apache2/000-default.conf /etc/apache2/sites-available/000-default.conf

# Exponemos el puerto 80 para el acceso web
EXPOSE 80

# Comando para iniciar Apache y PHP
CMD ["apache2-foreground"]