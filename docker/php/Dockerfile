FROM php:8.3-fpm

ARG USER_ID
ARG GROUP_ID

RUN apt update && \
    apt install -y zip libzip-dev libpng-dev libicu-dev libxml2-dev libcurl4-openssl-dev libonig-dev libwebp-dev libjpeg-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    ln -s /usr/local/bin/composer /usr/bin/composer

RUN docker-php-ext-install curl intl mbstring mysqli opcache xml zip
RUN docker-php-ext-configure gd --with-jpeg --with-webp

RUN apt clean

RUN groupadd -g $GROUP_ID appuser && \
    useradd -u $USER_ID -g appuser -m appuser
