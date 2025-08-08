FROM php:7.4-fpm
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini
# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libxslt1-dev \
    libpq-dev \
    libjpeg-dev \
    libpng-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo pdo_mysql xsl zip mbstring xml tokenizer

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --version=1.10.26 && \
    mv composer.phar /usr/bin/composer


WORKDIR /var/www/html

COPY . .

RUN composer install --no-interaction --optimize-autoloader

CMD ["php-fpm"]
