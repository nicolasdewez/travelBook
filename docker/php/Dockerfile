FROM php:7.1-fpm

RUN apt-get update && \
    apt-get install -y \
        libicu-dev \
        libjpeg-dev \
        libpng12-dev \
        libpq-dev \
        git && \
    docker-php-ext-install \
        mbstring \
        bcmath \
        intl \
        opcache \
        zip \
        pdo \
        pdo_pgsql && \
    pecl install apcu-5.1.3 && \
    docker-php-ext-enable apcu && \
    echo "date.timezone = Europe/Paris" >> /usr/local/etc/php/conf.d/symfony.ini && \
    echo "short_open_tag = Off" >> /usr/local/etc/php/conf.d/symfony.ini

# CLEAN
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Composer
COPY install-composer /usr/local/bin/install-composer
RUN /usr/local/bin/install-composer

RUN usermod -u 1001 www-data
