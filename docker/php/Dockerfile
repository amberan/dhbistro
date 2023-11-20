FROM php:8.2-fpm

# Installing dependencies for building the PHP modules
RUN apt update && \
    apt install -y zip libzip-dev libpng-dev libicu-dev libxml2-dev libcurl4-openssl-dev libonig-dev

# Installing additional PHP modules
RUN docker-php-ext-install mysqli pdo pdo_mysql gd zip intl xml gd curl mbstring composer

# Cleaning APT cache
RUN apt clean

# installing external libraries
RUN cd /var/www/html/ && composer install
