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

# 2. تفعيل Rewrite (ضروري لعمل روابط لارفيل)
RUN a2enmod rewrite

# 3. تثبيت Composer بشكل رسمي
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. ضبط الـ Document Root ليشير إلى مجلد public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf

# 5. ضبط مسار العمل
WORKDIR /var/www/html

# 6. نسخ ملفات الحزم أولاً لتحسين سرعة البناء (Cache)
COPY composer.json composer.lock ./

# 7. تثبيت المكتبات بدون ملفات التطوير
RUN composer install --no-dev --no-scripts --no-interaction --optimize-autoloader

# 8. نسخ باقي ملفات المشروع بالكامل
COPY . /var/www/html

# 9. توليد خريطة الملفات (Autoload)
RUN composer dump-autoload --optimize

# 10. تجهيز قاعدة بيانات SQLite وصلاحيات المجلدات
# تم إضافة إنشاء مجلد وقاعدة بيانات sqlite لضمان عدم حدوث خطأ 500
RUN mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache

# 11. ضبط الصلاحيات النهائية (مهم جداً لـ Render)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/database

# 12. نقطة الانطلاق: تشغيل الكاش، المايجريشن، ثم السيرفر
# تم دمج الأوامر لضمان أن قاعدة البيانات جاهزة قبل فتح الموقع
ENTRYPOINT ["/bin/sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force && apache2-foreground"]

EXPOSE 80