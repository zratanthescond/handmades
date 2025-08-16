# Étape 1: Construire les assets frontend avec Node.js
FROM node:18-alpine AS node_builder
WORKDIR /app
COPY package*.json yarn.lock ./
RUN yarn install --frozen-lockfile
COPY . .
# Ajout de NODE_OPTIONS pour résoudre le problème OpenSSL avec les anciennes versions de Webpack/Encore
ENV NODE_OPTIONS=--openssl-legacy-provider
RUN yarn build
RUN mkdir -p public/build # Ajout de cette ligne pour créer le répertoire

# Étape 2: Préparer l\"environnement PHP de base
FROM php:8.3-fpm-alpine AS php_base

# Variables d\"environnement pour une configuration non interactive
ENV APCU_VERSION=5.1.22
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installation des dépendances
# 1. Dépendances de build (seront supprimées)
# 2. Dépendances d\"exécution (seront conservées)
# 3. Extensions PHP
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    autoconf \
    icu-dev \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libxslt-dev \
    oniguruma-dev \
    postgresql-dev \
    && apk add --no-cache \
    bash \
    curl \
    git \
    nginx \
    supervisor \
    icu-libs \
    libzip \
    libpng \
    libjpeg-turbo \
    freetype \
    libwebp \
    libxslt \
    postgresql-libs \
    # Installation des extensions PHP
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
    bcmath \
    gd \
    intl \
    mbstring \
    opcache \
    pdo_mysql \
    pdo_pgsql \
    zip \
    xml \
    xsl \
    # Compilation et installation de APCu
    && pecl install apcu-${APCU_VERSION} \
    && docker-php-ext-enable apcu \
    # Nettoyage : on supprime UNIQUEMENT les dépendances de build
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/*

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configuration de PHP-FPM pour utiliser un socket et écouter correctement
COPY zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

# Configuration de PHP pour la production
COPY 99-symfony.ini /usr/local/etc/php/conf.d/99-symfony.ini

WORKDIR /var/www/html

# Étape 3: Construire l\"application Symfony
FROM php_base AS app_builder
WORKDIR /var/www/html

# Installer les dépendances Composer en optimisant le cache
COPY composer.json composer.lock ./
RUN composer install --prefer-dist --no-dev --no-scripts --no-progress --no-interaction

# Copier le reste de l\"application
COPY . .

# Étape 4: Créer l\"image finale de production
FROM php_base AS app_final
WORKDIR /var/www/html

# Copier les fichiers de l\"application et les assets depuis les étapes précédentes
COPY --from=app_builder /var/www/html .
COPY --from=node_builder /app/public/build ./public/build

# Exécuter les scripts Composer post-install et post-update
# Nous allons maintenant nous assurer que MakerBundle n'est pas chargé en production
# en modifiant le fichier bundles.php directement dans le Dockerfile
# ou en s'assurant que composer install --no-dev est suffisant.
# Pour l'instant, nous allons commenter l'exécution des scripts post-install ici.
# RUN composer run-script post-install-cmd --no-dev
# RUN composer run-script post-update-cmd --no-dev

# Configuration de Nginx
COPY nginx.conf /etc/nginx/nginx.conf

# Configuration de Supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Définir les permissions correctes pour l\"exécution
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