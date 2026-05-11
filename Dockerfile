FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    git unzip curl zip \
    libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip

# install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN a2enmod rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
 /etc/apache2/sites-available/*.conf \
 /etc/apache2/apache2.conf \
 /etc/apache2/conf-available/*.conf

COPY . /var/www/html

WORKDIR /var/www/html

# 👇 أهم سطر
RUN composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

EXPOSE 80