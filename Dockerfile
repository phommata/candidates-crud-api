FROM php:7.4

WORKDIR /var/www/html

COPY --from=composer /usr/bin/composer /usr/bin/composer

COPY . .

RUN apt-get update \
    && apt-get --no-install-recommends install -y \
        unzip \
    && pecl install xdebug \
    && docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-enable xdebug \
    && composer install --no-scripts --no-suggest --no-interaction --prefer-dist

COPY build/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

CMD php -S 0.0.0.0:80 -t public

ENV PHP_IDE_CONFIG="serverName=promenade.co"
