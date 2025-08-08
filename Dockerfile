### Stage 1: Build PHP dependencies and assets
FROM php:8.1-fpm-alpine AS build

RUN apk add --no-cache \
    bash git unzip curl openssl \
    libzip-dev libpng-dev libjpeg-turbo-dev freetype-dev icu-dev \
    libxml2-dev oniguruma-dev g++ make autoconf nodejs npm

RUN docker-php-ext-install pdo pdo_mysql intl zip xml opcache

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.* ./

RUN composer install --no-dev --prefer-dist --optimize-autoloader

COPY . .

RUN npm install && npm run build

### Stage 2: Production image
FROM php:8.1-fpm-alpine

RUN apk add --no-cache \
    bash curl nginx openssl supervisor \
    icu libpng libjpeg-turbo freetype libzip oniguruma

WORKDIR /app

COPY --from=build /app /app
COPY --from=build /usr/bin/composer /usr/bin/composer

RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data /app

COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
