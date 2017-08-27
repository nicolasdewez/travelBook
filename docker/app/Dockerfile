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
    echo "short_open_tag = Off" >> /usr/local/etc/php/conf.d/symfony.ini && \
    pecl install xdebug && echo zend_extension=/usr/local/lib/php/extensions/no-debug-non-zts-20160303/xdebug.so > /usr/local/etc/php/xdebug.ini.sample

# Blackfire PHP extension
RUN export VERSION=`php -r "echo PHP_MAJOR_VERSION.PHP_MINOR_VERSION;"` \
    && curl -A "Docker" -o /tmp/blackfire-probe.tar.gz -D - -L -s https://blackfire.io/api/v1/releases/probe/php/linux/amd64/${VERSION} \
    && tar zxpf /tmp/blackfire-probe.tar.gz -C /tmp \
    && mv /tmp/blackfire-*.so `php -r "echo ini_get('extension_dir');"`/blackfire.so \
    && rm -f /tmp/blackfire-probe.tar.gz \
    && echo "extension=blackfire.so\nblackfire.agent_socket=\${BLACKFIRE_PORT}" > $PHP_INI_DIR/conf.d/blackfire.ini

# Blackfire CLI tool
RUN curl -sL https://packagecloud.io/gpg.key | apt-key add - \
    && echo "deb http://packages.blackfire.io/debian any main" > /etc/apt/sources.list.d/blackfire.list \
    && apt-get update && apt-get install --yes blackfire-agent \
    && rm -rf /var/lib/apt/lists/*

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

# Scripts to execute at runtime
ADD entrypoint.sh /usr/local/bin/entrypoint.sh
ADD entrypoint.d /usr/local/bin/entrypoint.d

EXPOSE 9000

ENTRYPOINT ["entrypoint.sh"]

CMD  ["php-fpm"]
