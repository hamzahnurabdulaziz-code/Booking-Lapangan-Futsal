# syntax=docker/dockerfile:1

# ==========================================
# Stage 1: Build Frontend Assets with Node & Vite
# ==========================================
FROM node:22-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

# ==========================================
# Stage 2: Install PHP Composer Dependencies
# ==========================================
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --no-scripts \
    --optimize-autoloader

# ==========================================
# Stage 3: Production Runtime with FrankenPHP
# ==========================================
FROM dunglas/frankenphp:1-php8.4-alpine AS runtime

# Use production PHP settings
RUN cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

# Custom PHP settings for production
RUN echo "memory_limit = 256M" >> "$PHP_INI_DIR/conf.d/futsalkita.ini" && \
    echo "upload_max_filesize = 10M" >> "$PHP_INI_DIR/conf.d/futsalkita.ini" && \
    echo "post_max_size = 12M" >> "$PHP_INI_DIR/conf.d/futsalkita.ini" && \
    echo "opcache.enable = 1" >> "$PHP_INI_DIR/conf.d/futsalkita.ini" && \
    echo "opcache.validate_timestamps = 0" >> "$PHP_INI_DIR/conf.d/futsalkita.ini"

# Install required PHP extensions for Laravel & MySQL
RUN install-php-extensions \
    pdo_mysql \
    pdo_pgsql \
    intl \
    zip \
    gd \
    bcmath \
    opcache

WORKDIR /app

# Copy application source code
COPY . /app

# Copy production vendor dependencies
COPY --from=vendor /app/vendor /app/vendor

# Copy compiled Vite assets
COPY --from=frontend /app/public/build /app/public/build

# Copy Caddy configuration for FrankenPHP
COPY Caddyfile /etc/caddy/Caddyfile
COPY Caddyfile /etc/frankenphp/Caddyfile

# Ensure write permissions for Laravel storage and cache directories
RUN mkdir -p /app/storage/framework/cache/data \
             /app/storage/framework/sessions \
             /app/storage/framework/views \
             /app/storage/logs \
             /app/bootstrap/cache && \
    chmod -R 775 /app/storage /app/bootstrap/cache && \
    chown -R www-data:www-data /app/storage /app/bootstrap/cache

# Dynamic port binding (Vercel assigns $PORT dynamically)
ENV PORT=80
EXPOSE 80

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
