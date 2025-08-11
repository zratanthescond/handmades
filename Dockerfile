# Stage 1: Build the Composer dependencies
FROM composer:2 AS composer_builder

WORKDIR /app
COPY composer.* ./
RUN composer install --no-dev --no-autoloader --no-scripts --optimize-autoloader

# Stage 2: Build the PHP-FPM environment with the application code
FROM php:7.4-fpm-alpine AS symfony_app

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies
# These are the C libraries needed by PHP extensions, not the PHP extensions themselves.
RUN apk add --no-cache \
    nginx \
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

# Copy the Composer dependencies from the previous stage
COPY --from=composer_builder /app/vendor /var/www/html/vendor

# Copy the application code
COPY --chown=www-data:www-data . .

# Set up permissions for Symfony directories and clean up
RUN set -eux; \
    mkdir -p var/cache var/log; \
    chown -R www-data:www-data var public; \
    rm -rf /var/cache/apk/*

# Copy Nginx configuration and startup script
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/nginx/start.sh /usr/local/bin/start.sh

# Set correct file permissions and make the script executable
RUN chown -R www-data:www-data /var/www/html && \
    chmod +x /usr/local/bin/start.sh

# Switch to non-root user
USER www-data

# Expose port 80
EXPOSE 80

# The startup script runs both Nginx and PHP-FPM
CMD ["/usr/local/bin/start.sh"]