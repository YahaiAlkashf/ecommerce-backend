FROM php:8.2-fpm-alpine 

RUN apk add --no-cache --update \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    zip \
    intl \
    mbstring \
    xml \
    && docker-php-ext-configure intl \
    && docker-php-ext-enable intl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader


RUN cp .env.example .env || true


RUN php artisan key:generate

EXPOSE 8080

CMD ["php-fpm"]