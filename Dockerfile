# Stage 1: Build the Composer dependencies
FROM composer:2 AS composer_builder

# Set the working directory
WORKDIR /app

# Copy Composer files
COPY composer.* ./

# Install dependencies in a separate step to isolate errors
RUN composer install --no-dev --no-autoloader --optimize-autoloader

#-----------------------------------------------------------------------------------------------------------------

# Stage 2: The final production image with PHP-FPM and Nginx
FROM php:7.4-fpm

# Set the working directory
WORKDIR /var/www/html

# Install system dependencies and Nginx
RUN apt-get update && apt-get install -y \
    nginx \
    git \
    curl \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    libicu-dev \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Copy Composer from the builder stage
COPY --from=composer_builder /app/vendor /var/www/html/vendor

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    gd \
    pdo \
    pdo_pgsql \
    opcache \
    mbstring \
    xml \
    tokenizer \
    zip \
    intl

# Copy application code
COPY --chown=www-data:www-data . .

# Set permissions for Symfony directories
RUN set -eux; \
    mkdir -p var/cache var/log; \
    chown -R www-data:www-data var public;

# Copy custom Nginx configuration
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Copy the startup script
COPY docker/nginx/start.sh /usr/local/bin/start.sh

# Set file permissions
RUN chmod +x /usr/local/bin/start.sh && \
    chown -R www-data:www-data /var/www/html

# Switch to non-root user
USER www-data

# Expose port 80
EXPOSE 80

# Run the startup script
CMD ["/usr/local/bin/start.sh"]