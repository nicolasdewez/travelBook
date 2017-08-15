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
        pcntl \
        pdo \
        pdo_pgsql && \
    pecl install apcu-5.1.3 && \
    docker-php-ext-enable apcu && \
    echo "date.timezone = Europe/Paris" >> /usr/local/etc/php/conf.d/symfony.ini && \
    echo "short_open_tag = Off" >> /usr/local/etc/php/conf.d/symfony.ini

# Yarn
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash -
RUN apt-get install nodejs

RUN curl -sS https://dl.yarnpkg.com/debian/pubkey.gpg | apt-key add -
RUN echo "deb https://dl.yarnpkg.com/debian/ stable main" | tee /etc/apt/sources.list.d/yarn.list
RUN apt-get install apt-transport-https && \
    apt-get update && \
    apt-get install -y yarn

RUN mkdir /var/www/.config && \
    chmod -R 777 /var/www/.config && \
    mkdir /var/www/.cache && \
    chmod -R 777 /var/www/.cache && \
    touch /var/www/.yarnrc && \
    chmod 777 /var/www/.yarnrc

# Graphviz
RUN apt-get install -y graphviz

# CLEAN
RUN apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

# Composer
COPY install-composer /usr/local/bin/install-composer
RUN /usr/local/bin/install-composer

RUN usermod -u 1001 www-data
