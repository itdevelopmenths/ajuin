# Stage 1: Build Node.js assets
FROM node:20-alpine AS node_build
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# Stage 2: PHP Apache
FROM php:8.4-apache

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    libpq-dev \
    libonig-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd zip

# Raise default PHP upload limits (2M/8M) so multi-file attachment uploads aren't
# rejected by PHP before Laravel's own per-file validation (max:2048) runs
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/uploads.ini

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Change document root to public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY . .

# Copy Node.js build assets
COPY --from=node_build /app/public/build ./public/build

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Install PHP dependencies
RUN composer install --no-interaction --optimize-autoloader --no-dev

# Create missing directories and set permissions
RUN mkdir -p /var/www/html/storage/framework/views \
    && mkdir -p /var/www/html/storage/framework/cache \
    && mkdir -p /var/www/html/storage/framework/sessions \
    && mkdir -p /var/www/html/bootstrap/cache \
    && mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/app/public \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Link public/storage -> storage/app/public so ticket attachments are servable
# (must run at container start too, in case storage/ is mounted as a volume at runtime)
RUN php artisan storage:link

EXPOSE 80
CMD ["sh", "-c", "php artisan storage:link --force && apache2-foreground"]
