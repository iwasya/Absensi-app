FROM php:8.2-cli

# Install dependency
RUN apt-get update && apt-get install -y \
    unzip curl git libzip-dev zip libpng-dev libjpeg-dev libfreetype-dev libpq-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_pgsql gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working dir
WORKDIR /var/www

# Copy project
COPY . .

# Install Laravel dependency
RUN composer install --no-dev --optimize-autoloader

# Generate key (optional kalau belum)
RUN php artisan key:generate || true

# Expose port
EXPOSE 10000

# Run Laravel
CMD php -S 0.0.0.0:10000 -t public
