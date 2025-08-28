# -------- Stage 1: Composer install (prod) --------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
COPY . .
RUN composer install --no-dev --prefer-dist --no-ansi --no-interaction --no-progress --optimize-autoloader

# -------- Stage 2: Build Frontend (Vite) --------
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY vite.config.* postcss.config.* tailwind.config.* ./
COPY resources ./resources
COPY public ./public
# ✅ Ensure vendor exists for flux.css
COPY --from=vendor /app/vendor ./vendor
RUN npm run build

# -------- Stage 3: Runtime (Nginx + PHP-FPM) --------
FROM php:8.2-fpm-alpine AS runtime

RUN apk add --no-cache \
    bash curl nginx supervisor \
    icu-dev oniguruma-dev libzip-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    postgresql-dev mysql-client \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd intl mbstring zip pdo pdo_mysql pdo_pgsql opcache

WORKDIR /var/www

# Copy app (with vendor) from Composer stage
COPY --from=vendor /app /var/www

# Copy Vite build
COPY --from=frontend /app/public/build /var/www/public/build
