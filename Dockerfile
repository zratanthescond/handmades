
FROM php:8.3-fpm

# Install dependencies
RUN apt-get update && apt-get install -y     git unzip libicu-dev libxml2-dev libzip-dev zip     && docker-php-ext-install intl pdo pdo_mysql xml zip opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

RUN composer install --no-dev --optimize-autoloader

CMD ["php-fpm"]
