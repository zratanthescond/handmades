#!/bin/sh

# Start PHP-FPM in the background
php-fpm7 -F &

# Start Nginx in the foreground
nginx -g 'daemon off;'