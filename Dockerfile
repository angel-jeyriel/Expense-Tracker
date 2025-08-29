# -------- Stage 1: Composer install (with intl) --------
FROM composer:2 AS vendor
WORKDIR /app

# Install PHP extensions needed by Composer
RUN apk add --no-cache icu-dev oniguruma-dev libzip-dev \
    && docker-php-ext-install intl

COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-ansi --no-interaction --no-progress --optimize-autoloader
COPY . .

# -------- Stage 2: Build Frontend (Vite) --------
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY vite.config.* postcss.config.* tailwind.config.* ./
COPY resources ./resources
COPY public ./public
# Copy vendor so flux.css and other assets resolve correctly
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# -------- Stage 3: Runtime (Nginx + PHP-FPM) --------
FROM php:8.2-fpm-alpine AS runtime

# Install system deps + PHP extensions
RUN apk add --no-cache \
    bash curl nginx supervisor \
    icu-dev oniguruma-dev libzip-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    postgresql-dev mysql-client \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd intl mbstring zip pdo pdo_mysql pdo_pgsql opcache

WORKDIR /var/www

# Copy application (with vendor) from Composer stage
COPY --from=vendor /app /var/www

# Copy Vite build
COPY --from=frontend /app/public/build /var/www/public/build

# Copy nginx + supervisor configs
COPY ./docker/nginx.conf /etc/nginx/nginx.conf
COPY ./docker/supervisord.conf /etc/supervisord.conf

# Expose port 80
EXPOSE 80

# Start Supervisor (manages PHP-FPM + Nginx)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
