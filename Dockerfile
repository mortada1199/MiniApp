FROM php:8.2-apache

# dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl zip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# enable rewrite (Laravel required)
RUN a2enmod rewrite

# set correct document root
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# fix Apache config
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
 /etc/apache2/sites-available/*.conf \
 /etc/apache2/apache2.conf \
 /etc/apache2/conf-available/*.conf

# copy project
COPY . /var/www/html

WORKDIR /var/www/html

# install dependencies
RUN composer install --no-dev --optimize-autoloader

# 🔥 IMPORTANT FIX (سبب forbidden غالباً)
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 755 /var/www/html/public \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

EXPOSE 80