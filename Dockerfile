FROM php:8.2-fpm

# تثبيت المتطلبات
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libwebp-dev \
    libzip-dev

# إضافات PHP
RUN docker-php-ext-install pdo pdo_mysql zip exif

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد مجلد العمل
WORKDIR /var/www

# نسخ المشروع
COPY . .

# صلاحيات
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# تثبيت الباكجات
RUN composer install --no-dev --optimize-autoloader

# إعداد Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# 🔥 تشغيل migration تلقائيًا عند التشغيل
CMD php artisan db:seed --force && \
    php-fpm -D && \
    nginx -g 'daemon off;'
