FROM php:8.2-apache

RUN apt-get update && apt-get install -y git unzip zip curl libssl-dev pkg-config libcurl4-openssl-dev

RUN pecl install mongodb-1.17.1 && docker-php-ext-enable mongodb

COPY . /var/www/html/
WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

ENV APACHE_PORT=80
RUN sed -i 's/Listen 80/Listen ${PORT}/g' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost \*:${PORT}>/g' /etc/apache2/sites-available/000-default.conf

CMD ["apache2-foreground"]
