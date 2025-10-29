# ==========================
# 🔹 ETAPA 1: BUILD FRONTEND (Vite)
# ==========================
FROM node:18 AS build-frontend
WORKDIR /app

COPY package*.json vite.config.js ./
ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}

COPY resources ./resources
COPY public ./public

RUN npm install && npm run build


# ==========================
# 🔹 ETAPA 2: BACKEND (Laravel + PHP 8.2 + PostgreSQL)
# ==========================
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pgsql pdo mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-enable pdo_pgsql pgsql

# Aumentar limite de upload PHP
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini

WORKDIR /var/www/html

COPY . .
COPY --from=build-frontend /app/public/build ./public/build

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN php -r "if (!file_exists('.env')) copy('.env.example', '.env');"
RUN mkdir -p storage/app/public && \
    php artisan storage:link || true && \
    chown -R www-data:www-data storage bootstrap/cache

RUN php artisan key:generate || true

EXPOSE 8000

CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan migrate --force || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
