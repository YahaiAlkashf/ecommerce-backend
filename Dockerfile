FROM php:8.2-apache

WORKDIR /var/www/html


RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql zip mbstring gd


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


COPY . .


RUN composer install --no-dev --optimize-autoloader


RUN chmod -R 775 storage bootstrap/cache

CMD ["apache2-foreground"]