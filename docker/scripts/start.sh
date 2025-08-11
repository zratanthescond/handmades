#!/bin/sh

set -e

echo "Starting Symfony application..."

# Wait for database if needed
if [ -n "$DATABASE_URL" ]; then
    echo "Waiting for database..."
    until php bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
        echo "Database is unavailable - sleeping"
        sleep 1
    done
    echo "Database is up!"
fi

# Clear and warm up cache
echo "Clearing cache..."
php bin/console cache:clear --env=prod --no-debug

echo "Warming up cache..."
php bin/console cache:warmup --env=prod --no-debug

# Run database migrations
if [ -n "$DATABASE_URL" ]; then
    echo "Running database migrations..."
    php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration
fi

# Install assets
echo "Installing assets..."
php bin/console assets:install --env=prod --no-debug

# Set proper permissions
echo "Setting permissions..."
chown -R www-data:www-data /var/www/html/var
chmod -R 775 /var/www/html/var

echo "Starting services..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf

