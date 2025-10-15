FROM php:8.3-fpm-alpine

# System & Build
RUN set -eux; \
    apk add --no-cache --virtual .build-deps $PHPIZE_DEPS libzip-dev oniguruma-dev zlib-dev \
    && apk add --no-cache git unzip curl

# PHP Extensions
RUN docker-php-ext-install -j$(nproc) pdo_mysql zip mbstring opcache

# PECL: Xdebug + Redis (phpredis)
RUN apk add --update linux-headers \
    && pecl install xdebug redis \
    && docker-php-ext-enable xdebug redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# PHP-Inis
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY docker/php/xdebug.ini /usr/local/etc/php/conf.d/zz-xdebug.ini

WORKDIR /var/www/html

# simple perms for dirs
RUN addgroup -g 1000 app && adduser -D -G app -u 1000 app \
    && chown -R app:app /var/www/html
USER app

# Standard: Xdebug off (enable via ENV in compose)
ENV XDEBUG_MODE=off
EXPOSE 9000 9003