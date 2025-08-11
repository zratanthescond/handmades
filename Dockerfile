# Use a Debian-based PHP 7.4 FPM image
FROM php:7.4-fpm

# Set the working directory inside the container
WORKDIR /var/www/html

# Install system dependencies and Nginx
# Using apt-get is more reliable for these packages
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

# Install Composer from a dedicated image
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Install PHP extensions
# We use the correct tool for the job.
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

# Install Composer dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Set permissions for Symfony directories
RUN set -eux; \
    mkdir -p var/cache var/log; \
    chown -R www-data:www-data var public;

# Copy our custom Nginx configuration
COPY docker/nginx/default.conf /etc/nginx/sites-available/default
# Create symlink so Nginx uses the new config
RUN ln -s /etc/nginx/sites-available/default /etc/nginx/sites-enabled/

# Copy the startup script
COPY docker/nginx/start.sh /usr/local/bin/start.sh

# Set correct file permissions and make the script executable
RUN chmod +x /usr/local/bin/start.sh && \
    chown -R www-data:www-data /var/www/html

# Switch to the non-root user for security
USER www-data

# Expose port 80
EXPOSE 80

# The startup script runs both Nginx and PHP-FPM
CMD ["/usr/local/bin/start.sh"]