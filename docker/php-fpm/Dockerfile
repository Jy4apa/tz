FROM php:7.3-fpm

RUN docker-php-ext-install pdo pdo_mysql && \
    set -xe && \
    apt-get update -yqq && \
    pecl channel-update pecl.php.net && \
    apt-get install -y zlib1g-dev libicu-dev g++ libc-client-dev libkrb5-dev unzip git && \
    docker-php-ext-configure intl && \
    docker-php-ext-install intl && \
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install imap

# Composer install
ENV COMPOSER_ALLOW_SUPERUSER 1
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

