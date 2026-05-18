FROM php:8.2-cli

# Install dependency
RUN apt-get update && apt-get install -y \
    unzip curl git libzip-dev zip libpng-dev libjpeg-dev libfreetype-dev libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

RUN { \
    echo "opcache.enable=1"; \
    echo "opcache.enable_cli=1"; \
    echo "opcache.memory_consumption=96"; \
    echo "opcache.interned_strings_buffer=8"; \
    echo "opcache.max_accelerated_files=10000"; \
    echo "opcache.validate_timestamps=0"; \
    echo "realpath_cache_size=4096K"; \
    echo "realpath_cache_ttl=600"; \
} > /usr/local/etc/php/conf.d/opcache-performance.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www

# Copy project
COPY . .

# Install Laravel dependency
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

# Prepare writable Laravel runtime directories
RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 10000

# Run Laravel
CMD php artisan storage:link || true; php artisan optimize && php -S 0.0.0.0:${PORT:-10000} -t public
