FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libssl-dev pkg-config git unzip zip curl libcurl4-openssl-dev

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY . /var/www/html/
WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install

EXPOSE 80
