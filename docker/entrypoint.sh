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

echo "Ensuring SQLite database file exists..."
if [ "$DB_CONNECTION" = "sqlite" ] || [ -z "$DB_CONNECTION" ]; then
    mkdir -p /var/www/html/database
    touch /var/www/html/database/database.sqlite
    chmod 666 /var/www/html/database/database.sqlite
fi

echo "Caching configurations..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "Running migrations..."
php artisan migrate --force

if [ "$SEED_ON_DEPLOY" = "true" ]; then
    echo "Running seeders..."
    php artisan db:seed --force
fi

echo "Starting Supervisor..."
mkdir -p /var/log/supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
