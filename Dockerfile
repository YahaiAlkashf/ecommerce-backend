
FROM php:8.2-cli as builder

WORKDIR /app
COPY . .
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install zip pdo_mysql \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-dev --optimize-autoloader


FROM php:8.2-apache

WORKDIR /var/www/html
COPY --from=builder /app .
COPY --from=builder /app/public /var/www/html/public


RUN chown -R www-data:www-data /var/www/html \
    && a2enmod rewrite \
    && echo "ServerName localhost" >> /etc/apache2/apache2.conf


RUN chmod -R 775 storage bootstrap/cache


ENV APP_ENV=production
ENV APP_DEBUG=false

CMD ["apache2-foreground"]
