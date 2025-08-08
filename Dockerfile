# Stage 1: Build stage
FROM php:8.1-cli-alpine AS builder

# Install system dependencies
RUN apk add --no-cache \
    git unzip bash libxml2-dev libxslt-dev oniguruma-dev zlib-dev

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql xsl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set work directory
WORKDIR /app

# Copy application files
COPY . .

# Install PHP dependencies (production only)
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Stage 2: Runtime stage
FROM php:8.1-cli-alpine

# Install system dependencies
RUN apk add --no-cache bash libxml2 libxslt zlib

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql xsl

# Set timezone and working dir
ENV TZ=UTC
WORKDIR /app

# Copy from build stage
COPY --from=builder /app /app

# Expose default port
EXPOSE 8000

# Run Symfony web server
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
