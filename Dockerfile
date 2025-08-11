# Stage 1: Build the Composer dependencies
FROM composer:2 AS composer_builder

# Set the working directory
WORKDIR /app

# Copy Composer files and install dependencies
COPY composer.* ./
RUN composer install --no-dev --no-autoloader --no-scripts --optimize-autoloader

#-----------------------------------------------------------------------------------------------------------------

# Stage 2: Build the Symfony application with PHP-FPM
FROM php:7.4-fpm-alpine AS symfony_app

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies (C libraries) for PHP extensions
RUN apk add --no-cache \
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

# Copy our custom Nginx configuration and startup script
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY docker/nginx/start.sh /docker-entrypoint.sh

# Set correct permissions
RUN chmod +x /docker-entrypoint.sh

# Expose port 80
EXPOSE 80

# The startup script runs both Nginx and PHP-FPM
CMD ["/docker-entrypoint.sh"]