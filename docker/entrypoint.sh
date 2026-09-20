#!/bin/sh
set -e

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Ensuring SQLite database file exists..."
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chmod 666 /var/www/html/database/database.sqlite
fi

echo "Running migrations..."
php artisan migrate --force

if [ "$SEED_ON_DEPLOY" = "true" ]; then
    echo "Running seeders..."
    php artisan db:seed --force
fi

echo "Starting Supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
