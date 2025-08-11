# Stage 1: Build the Composer dependencies
FROM composer:2 AS composer_builder

WORKDIR /app
COPY composer.* ./
RUN composer install --no-dev --no-autoloader --no-scripts --optimize-autoloader

#-----------------------------------------------------------------------------------------------------------------

# Stage 2: Build the Symfony application with PHP-FPM
FROM php:7.4-fpm-alpine AS symfony_app

# Set the working directory
WORKDIR /var/www/html

# Update package lists and install system dependencies (C libraries) for PHP extensions
RUN apk update && apk add --no-cache \
    git \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql opcache mbstring xml tokenizer zip

# Copy the Composer dependencies from the first stage
COPY --from=composer_builder /app/vendor /var/www/html/vendor

# Copy the application code
COPY --chown=www-data:www-data . .

# Set permissions for Symfony directories and clean up
RUN set -eux; \
    mkdir -p var/cache var/log; \
    chown -R www-data:www-data var public; \
    rm -rf /var/cache/apk/*

#-----------------------------------------------------------------------------------------------------------------

# Stage 3: The final production image with Nginx
FROM nginx:stable-alpine

# Copy the built application from the second stage
COPY --from=symfony_app --chown=nginx:nginx /var/www/html /var/www/html

# Remove the default Nginx configuration
RUN rm /etc/nginx/conf.d/default.conf

# Copy our custom Nginx configuration
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# This is the key change: we embed the startup command directly.
# This runs both PHP-FPM and Nginx in the foreground.
CMD ["/bin/sh", "-c", "php-fpm7 -F & nginx -g 'daemon off;'"]