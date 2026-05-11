FROM php:8.2-apache

# 1. تثبيت الاعتمادات اللازمة للنظام ولـ PHP
RUN apt-get update && apt-get install -y \
    libpng-dev \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    libonig-dev \
    zip \
    curl \
    unzip \
    && docker-php-ext-configure gd \
    && docker-php-ext-install -j$(nproc) gd pdo_mysql mysqli zip opcache mbstring bcmath

# 2. تفعيل Rewrite
RUN a2enmod rewrite

# 3. تثبيت Composer بشكل رسمي
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. ضبط الـ Document Root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. نسخ ملفات composer أولاً
WORKDIR /var/www/html
COPY composer.json composer.lock ./

# 6. تثبيت المكتبات
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

# 7. نسخ باقي ملفات المشروع
COPY . /var/www/html

# 8. تنفيذ الـ Scripts الخاصة بـ composer
RUN composer dump-autoload --optimize

# 9. تجهيز قاعدة بيانات SQLite (هنا الحل)
RUN mkdir -p /var/www/html/database && \
    touch /var/www/html/database/database.sqlite

# 10. ضبط الصلاحيات (مهمة جداً للوصول للملفات وقاعدة البيانات)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/database

# 11. تشغيل المايجريشن تلقائياً عند بدء التشغيل
# استخدمنا سكريبت بسيط عشان يضمن تشغيل المايجريشن ثم تشغيل الأباتشي
ENTRYPOINT ["/bin/sh", "-c", "php artisan migrate --force && apache2-foreground"]

EXPOSE 80