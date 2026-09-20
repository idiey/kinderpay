FROM php:8.3-fpm-alpine

# Install system dependencies & PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    zip \
    unzip \
    git \
    libzip-dev \
    libpng-dev \
    oniguruma-dev \
    libxml2-dev \
    sqlite-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo_mysql pdo_sqlite mbstring xml gd zip bcmath opcache \
    && apk del $PHPIZE_DEPS

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files and install PHP dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Copy application code (including pre-compiled public/build)
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy configurations
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Make entrypoint executable
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port 80
EXPOSE 80

# Run entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
