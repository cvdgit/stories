FROM php:7.4-apache

ENV XDEBUG_VERSION 3.1.6

RUN apt-get update && apt-get install -y mariadb-client libjpeg62-turbo-dev libpng-dev libwebp-dev libfreetype6-dev libmcrypt-dev libicu-dev libzip-dev libssl-dev git \
    && git clone --depth 1 https://github.com/php/pecl-encryption-mcrypt.git /usr/src/php/ext/mcrypt \
    && docker-php-ext-configure mcrypt \
    && git clone -b $XDEBUG_VERSION --depth 1 https://github.com/xdebug/xdebug.git /usr/src/php/ext/xdebug \
    && docker-php-ext-configure xdebug --enable-xdebug-dev \
    && docker-php-ext-configure gd \
        --with-jpeg \
        --with-webp \
        --with-freetype \
    && docker-php-ext-install pdo_mysql gd mcrypt intl zip bcmath xdebug

RUN mv $PHP_INI_DIR/php.ini-development $PHP_INI_DIR/php.ini

COPY ./apache-php/conf.d /usr/local/etc/php/conf.d
COPY ./apache-php/default.conf /etc/apache2/sites-available/000-default.conf

RUN cp /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/ && \
    cp /etc/apache2/mods-available/headers.load /etc/apache2/mods-enabled/

COPY ./apache-php/cert/cert.key /etc/ssl/private/
COPY ./apache-php/cert/cert.crt /etc/ssl/certs/

RUN a2enmod ssl && a2enmod socache_shmcb

COPY ./apache-php/wait-for-it.sh /usr/local/bin/wait-for-it
RUN chmod 555 /usr/local/bin/wait-for-it

COPY --from=composer:1 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
