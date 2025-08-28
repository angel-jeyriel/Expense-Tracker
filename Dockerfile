# -------- Stage 1: Build Frontend (Vite) --------
FROM node:18-alpine AS frontend
WORKDIR /app
COPY package*.json ./
RUN npm ci
# copy only what's needed for Vite build
COPY vite.config.* postcss.config.* tailwind.config.* ./
COPY resources ./resources
COPY public ./public
RUN npm run build

# -------- Stage 2: Composer install (prod) --------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
# copy full source so Composer can optimize autoload properly
COPY . .
RUN composer install --no-dev --prefer-dist --no-ansi --no-interaction --no-progress --optimize-autoloader

# -------- Stage 3: Runtime (Nginx + PHP-FPM) --------
FROM php:8.2-fpm-alpine AS runtime

# System packages
RUN apk add --no-cache \
    bash curl nginx supervisor \
    icu-dev oniguruma-dev libzip-dev \
    freetype-dev libjpeg-turbo-dev libpng-dev \
    postgresql-dev mysql-client

# PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd intl mbstring zip pdo pdo_mysql pdo_pgsql opcache

WORKDIR /var/www

# Copy app (with vendor) from Composer stage
COPY --from=vendor /app /var/www

# Copy Vite build (default output is /public/build)
# If your Vite outputs to /public/dist instead, change the line below
COPY --from=frontend /app/public/build /var/www/public/build

# Nginx + Supervisor configs and entrypoint
COPY docker/nginx/default.conf.template /etc/nginx/http.d/default.conf.template
COPY docker/supervisord.conf /etc/supervisord.conf
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh && mkdir -p /run/nginx

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

# Ensure PHP-FPM listens on TCP (Nginx upstream)
ENV PHP_FPM_LISTEN=127.0.0.1:9000
# Render sets $PORT dynamically; default to 8080 for local runs
ENV PORT=8080

EXPOSE 8080
CMD ["/entrypoint.sh"]
