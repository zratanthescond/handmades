FROM php:8.3-apache

# Install system dependencies and PHP build deps including ca-certificates
RUN apt-get update && apt-get install -y \
    ca-certificates \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libpq-dev \
    libonig-dev \
    libxslt-dev \
    && update-ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        pdo \
        pdo_pgsql \
        opcache \
        mbstring \
        zip \
        intl \
        xsl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite headers

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better caching
COPY composer.json composer.lock ./

# Install dependencies without dev
RUN composer install --no-dev --no-scripts --no-autoloader --optimize-autoloader

# Copy application code
COPY . .

# Complete composer installation
RUN composer dump-autoload --optimize --no-dev

# Configure Apache for Symfony
RUN echo '<VirtualHost *:80>\n\
    ServerName localhost\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        DirectoryIndex index.php\n\
        \n\
        <IfModule mod_rewrite.c>\n\
            RewriteEngine On\n\
            RewriteCond %{HTTP:Authorization} .\n\
            RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]\n\
            RewriteCond %{REQUEST_FILENAME} !-f\n\
            RewriteRule ^(.*)$ index.php [QSA,L]\n\
        </IfModule>\n\
    </Directory>\n\
    \n\
    Header always set X-Content-Type-Options nosniff\n\
    Header always set X-Frame-Options DENY\n\
    Header always set X-XSS-Protection "1; mode=block"\n\
    \n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Configure PHP for production
RUN echo 'expose_php = Off\n\
max_execution_time = 30\n\
memory_limit = 256M\n\
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT\n\
display_errors = Off\n\
log_errors = On\n\
upload_max_filesize = 10M\n\
post_max_size = 10M\n\
session.cookie_httponly = 1\n\
session.use_strict_mode = 1\n\
date.timezone = Europe/Paris\n\
opcache.enable = 1\n\
opcache.memory_consumption = 128\n\
opcache.interned_strings_buffer = 8\n\
opcache.max_accelerated_files = 4000\n\
opcache.revalidate_freq = 2\n\
opcache.validate_timestamps = 0' > /usr/local/etc/php/conf.d/symfony.ini

# Set proper permissions
RUN mkdir -p var/cache var/log public/uploads \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 var public/uploads

# Create health check endpoint
RUN echo '<?php header("Content-Type: text/plain"); echo "healthy\n"; ?>' > /var/www/html/public/health.php

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/health.php || exit 1

CMD ["apache2-foreground"]
