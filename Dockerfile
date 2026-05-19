FROM php:8.2-cli

RUN apt-get update && apt-get install -y git unzip zip curl libssl-dev pkg-config libcurl4-openssl-dev

RUN pecl install mongodb-1.17.1 && docker-php-ext-enable mongodb

COPY . /var/www/html/
WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install

# Fix: Redirect the main root address straight to your designed HTML signup layout
RUN echo "<?php header('Location: /signup.html'); exit;" > index.php

CMD php -S 0.0.0.0:$PORT
