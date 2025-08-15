# Étape 1: Construire les assets frontend avec Node.js
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package*.json yarn.lock ./
RUN yarn install --frozen-lockfile
COPY . .
# Ajout de NODE_OPTIONS pour résoudre le problème OpenSSL avec les anciennes versions de Webpack/Encore
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN yarn build

# Étape 2: Préparer l'environnement PHP de base
FROM php:8.3-fpm-alpine AS php_base

# Variables d'environnement pour une configuration non interactive
ENV APCU_VERSION=5.1.22
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installation des dépendances système et des extensions PHP
RUN apk add --no-cache \
    bash \
    curl \
    git \
    nginx \
    supervisor \
    icu-dev \
    libzip-dev \
    libxml2-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    bcmath \
    gd \
    intl \
    mbstring \
    opcache \
    pdo_mysql \
    zip \
    xml \
    && pecl install apcu-${APCU_VERSION} \
    && docker-php-ext-enable apcu \
    && apk del --no-network .build-deps \
    && rm -rf /var/cache/apk/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration de PHP-FPM pour utiliser un socket et écouter correctement
COPY <<EOF /usr/local/etc/php-fpm.d/zz-docker.conf
[global]
daemonize = no

[www]
listen = /var/run/php-fpm.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 15
pm.start_servers = 2
pm.min_spare_servers = 1
pm.max_spare_servers = 3
EOF

# Configuration de PHP pour la production
COPY <<EOF /usr/local/etc/php/conf.d/99-symfony.ini
date.timezone = UTC
session.auto_start = Off
opcache.enable = 1
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.memory_consumption = 256
opcache.save_comments = 1
opcache.revalidate_freq = 0
opcache.fast_shutdown = 1
EOF

WORKDIR /var/www/html

# Étape 3: Construire l'application Symfony
FROM php_base AS app_builder
WORKDIR /var/www/html

# Installer les dépendances Composer en optimisant le cache
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction

# Copier le reste de l'application
COPY . .

# Exécuter les scripts Composer après avoir copié les fichiers
RUN composer run-script post-install-cmd --no-dev

# Étape 4: Créer l'image finale de production
FROM php_base AS app_final
WORKDIR /var/www/html

# Copier les fichiers de l'application et les assets depuis les étapes précédentes
COPY --from=app_builder /var/www/html .
COPY --from=node_builder /app/public/build ./public/build

# Configuration de Nginx
COPY <<EOF /etc/nginx/nginx.conf
user www-data;
worker_processes auto;
pid /run/nginx.pid;
error_log /dev/stderr warn;
include /etc/nginx/modules-enabled/*.conf;

events {
    worker_connections 1024;
}

http {
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    access_log /dev/stdout;
    sendfile on;
    keepalive_timeout 65;

    server {
        listen 80 default_server;
        server_name _;
        root /var/www/html/public;
        index index.php;

        location / {
            try_files \$uri /index.php\$is_args\$args;
        }

        location ~ ^/index\.php(/|$ ) {
            fastcgi_pass unix:/var/run/php-fpm.sock;
            fastcgi_split_path_info ^(.+\.php)(/.*)\$;
            include fastcgi_params;
            fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
            fastcgi_param DOCUMENT_ROOT \$realpath_root;
            internal;
        }

        location ~ \.php$ {
            return 404;
        }

        location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
        }

        # Endpoint pour le health check
        location /health {
            access_log off;
            return 200 "healthy";
            add_header Content-Type text/plain;
        }
    }
}
EOF

# Configuration de Supervisor
COPY <<EOF /etc/supervisor/conf.d/supervisord.conf
[supervisord]
nodaemon=true
user=root

[program:php-fpm]
command=/usr/local/sbin/php-fpm
autostart=true
autorestart=true
priority=1
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0

[program:nginx]
command=/usr/sbin/nginx -g "daemon off;"
autostart=true
autorestart=true
priority=2
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
EOF

# Définir les permissions correctes pour l'exécution
RUN mkdir -p var/cache var/log && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 775 /var/www/html/var

# Exposer le port 80
EXPOSE 80

# Healthcheck pour vérifier que Nginx est bien démarré et répond
HEALTHCHECK --interval=30s --timeout=5s --start-period=15s --retries=3 \
  CMD curl -f http://localhost/health || exit 1

# Commande de démarrage du conteneur
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
