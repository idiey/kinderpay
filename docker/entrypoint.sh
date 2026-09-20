#!/bin/sh
set -e

# 0. Ensure .env exists
if [ ! -f .env ]; then
    [ -f .env.example ] && cp .env.example .env || touch .env
fi

# 1. Ensure valid base64 APP_KEY
if [ -z "$APP_KEY" ] || ! echo "$APP_KEY" | grep -q "^base64:"; then
    export APP_KEY="base64:bsTZLkU7jUQ+jYxbm2TlQEcPejpNHwvaPyR0q4RLRrc="
fi

if grep -q "^APP_KEY=" .env; then
    sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
else
    echo "APP_KEY=${APP_KEY}" >> .env
fi

echo "Ensuring SQLite database file and directory permissions exist..."
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chown -R www-data:www-data /var/www/html/database
    chmod -R 775 /var/www/html/database
    chmod 666 /var/www/html/database/database.sqlite
fi

# Ensure storage & bootstrap permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Running migrations..."
php artisan migrate --force

echo "Ensuring seeders are applied..."
php artisan db:seed --force

# Crucial for SQLite: migrations and seeds run as root in entrypoint.
# Ensure www-data (PHP-FPM worker) has full write permission on both directory and sqlite files.
chown -R www-data:www-data /var/www/html/database /var/www/html/storage
chmod -R 777 /var/www/html/database
chmod -R 775 /var/www/html/storage

echo "Starting Supervisor..."
mkdir -p /var/log/supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
