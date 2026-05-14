FROM php:8.2-apache

# 1. تثبيت الاعتمادات اللازمة للنظام و PHP Extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    zip \
    curl \
    unzip \
    git \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo_mysql \
        mysqli \
        zip \
        opcache \
        mbstring \
        bcmath

# 2. تفعيل Apache Rewrite
RUN a2enmod rewrite

# 3. تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. ضبط Document Root إلى public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/apache2.conf

# 5. تحديد مجلد العمل
WORKDIR /var/www/html

# 6. نسخ ملفات Composer أولاً للاستفادة من Docker Cache
COPY composer.json composer.lock ./

# 7. تثبيت مكتبات Laravel 
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --ignore-platform-reqs \
    --optimize-autoloader

# 8. نسخ المشروع بالكامل
COPY . /var/www/html

# 9. توليد Autoload
RUN composer dump-autoload --optimize

# 10. إنشاء SQLite وتجهيز المجلدات
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/logs

# 11. إنشاء Storage Link
RUN php artisan storage:link || true

# 12. ضبط الصلاحيات (أعطينا صلاحية 777 للمجلد database لضمان كتابة SQLite)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 777 /var/www/html/database

# 13. تشغيل Laravel
# تم تعديل الترتيب: نقوم بإنشاء الجداول أولاً (migrate) ثم مسح الكاش
# أضفنا --ignore-platform-reqs للأوامر لضمان عدم التوقف
ENTRYPOINT ["/bin/sh", "-c", "\
php artisan migrate --force && \
php artisan config:clear && \
php artisan cache:clear || true && \
php artisan view:clear && \
php artisan config:cache && \
php artisan view:cache && \
apache2-foreground"]

# 14. فتح البورت 80
EXPOSE 80