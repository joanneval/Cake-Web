FROM php:8.2-apache

RUN apt-get update && apt-get install -y git unzip zip curl libssl-dev pkg-config libcurl4-openssl-dev

RUN pecl install mongodb && docker-php-ext-enable mongodb

COPY . /var/www/html/
WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

EXPOSE 80
