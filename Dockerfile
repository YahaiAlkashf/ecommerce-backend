# استخدم صورة PHP مناسبة كصورة أساسية
FROM php:8.1-fpm-alpine

# قم بتثبيت ملحقات PHP الضرورية
RUN apk add --no-cache \
    curl \
    libzip-dev \
    unzip

RUN docker-php-ext-install pdo_mysql zip

# قم بتثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# قم بتعيين مجلد العمل داخل الحاوية
WORKDIR /var/www/html

# انسخ ملفات مشروع Laravel إلى الحاوية
COPY . /var/www/html

# قم بتثبيت الاعتماديات
RUN composer install --no-dev --optimize-autoloader

# قم بإنشاء نسخة من ملف env
RUN cp .env.example .env

# قم بإنشاء مفتاح التطبيق
RUN php artisan key:generate --force

# قم بتعيين الأذونات المناسبة
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
