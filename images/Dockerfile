FROM php:7.4-apache AS base

RUN a2enmod rewrite
RUN apt-get update && apt-get install -y wget libzip-dev libonig-dev libcurl4-openssl-dev && apt-get clean
RUN docker-php-ext-install curl zip mbstring json
EXPOSE 80

FROM base AS composer

RUN wget https://raw.githubusercontent.com/composer/getcomposer.org/bfd95e2a4383ee9cf7c058c2df29d7acb5f86d77/web/installer -O - -q | php -- --quiet && \
    mv composer.phar /usr/bin/composer && \
    chmod +x /usr/bin/composer
COPY composer.json /var/www
COPY composer.lock /var/www
RUN cd /var/www && \
    composer install

FROM base as source
COPY --from=composer /var/www/vendor /var/www/vendor/
COPY src/ /var/www/html

FROM source AS development
RUN pecl install xdebug && docker-php-ext-enable xdebug
COPY images/xdebug.ini /tmp/xdebug.ini
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
RUN cat /tmp/xdebug.ini >> $PHP_INI_DIR/php.ini

FROM source AS production
RUN mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
