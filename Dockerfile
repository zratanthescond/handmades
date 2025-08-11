FROM php:7.4-fpm-alpine

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies and Nginx
# These are the C libraries needed by PHP extensions and Nginx itself
RUN apk add --no-cache \
    nginx \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev

# Install PHP extensions using the dedicated Docker command
RUN docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql opcache mbstring xml tokenizer zip

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy application code
COPY --chown=www-data:www-data . .

# Install Composer dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions for Symfony directories and clean up
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