FROM php:8.2-fpm

COPY ./php.ini /usr/local/etc/php/conf.d/php-custom.ini

RUN apt-get update && apt-get install zip unzip

RUN docker-php-ext-install mysqli pdo pdo_mysql

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f xdebug && docker-php-ext-enable xdebug

ENV COMPOSER_ALLOW_SUPERUSER 1 

WORKDIR /data

VOLUME /data

CMD ["php-fpm"]
