# ==========================
# 🔹 BUILD FRONTEND
# ==========================
FROM node:18 AS build-frontend
WORKDIR /app

COPY package*.json vite.config.js ./
ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}

COPY resources ./resources
COPY public ./public

RUN npm install
RUN npm run build

# ==========================
# 🔹 BUILD BACKEND (PHP + PostgreSQL)
# ==========================
FROM php:8.2-fpm

# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_pgsql pgsql pdo mbstring exif pcntl bcmath gd \
    && docker-php-ext-enable pdo_pgsql pgsql


WORKDIR /var/www/html

COPY . .

# Copia build do frontend
COPY --from=build-frontend /app/public/build ./public/build

# Instala Composer e dependências Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Prepara ambiente Laravel
RUN php -r "if (!file_exists('.env')) copy('.env.example', '.env');"
RUN php artisan key:generate || true
RUN php artisan storage:link || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
