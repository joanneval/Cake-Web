FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    libssl-dev pkg-config git unzip zip curl

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb

COPY . /var/www/html/

WORKDIR /var/www/html

RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

RUN composer install

EXPOSE 80