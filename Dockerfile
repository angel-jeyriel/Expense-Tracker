# -------- Stage 1: Composer install (with intl) --------
FROM composer:2 AS vendor
WORKDIR /app

# Install PHP extensions required for composer (intl, etc.)
RUN apk add --no-cache icu-dev oniguruma-dev libzip-dev \
    && docker-php-ext-install intl

# Copy all project files (so artisan exists during composer install)
COPY . .

# Install dependencies (production only)
RUN composer install --no-dev --prefer-dist --no-ansi --no-interaction --no-progress --optimize-autoloader


# -------- Stage 2: Build Frontend (Vite) --------
FROM node:20 AS frontend
WORKDIR /app

# Copy frontend sources
COPY package.json package-lock.json ./
RUN npm install

# Copy the rest of the project (for Vite build)
COPY . .

# Build frontend assets
RUN npm run build


# -------- Stage 3: Production runtime (PHP-FPM + Nginx) --------
FROM php:8.3-fpm-alpine AS runtime
WORKDIR /var/www/html

# Install system dependencies
RUN apk add --no-cache bash nginx supervisor icu libzip curl git

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql intl

# Copy vendor dependencies from Stage 1
COPY --from=vendor /app/vendor ./vendor

# Copy built frontend assets from Stage 2
COPY --from=frontend /app/public/build ./public/build

# Copy the rest of the app
COPY . .

# Set correct permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy Nginx and Supervisor configs
COPY docker/nginx.conf /etc/nginx/nginx.conf
COPY docker/supervisord.conf /etc/supervisord.conf

# Expose HTTP port
EXPOSE 80

# Run Supervisor (manages php-fpm + nginx)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
