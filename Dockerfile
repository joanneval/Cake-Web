FROM php:8.2-apache

RUN apt-get update && apt-get install -y git unzip zip curl \
    && curl -sSL https://github.com -o /usr/local/bin/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions mongodb

COPY . /var/www/html/
WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install

EXPOSE 80
