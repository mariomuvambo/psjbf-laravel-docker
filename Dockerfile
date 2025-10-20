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

# Instala libs e extensões obrigatórias, incluindo pdo_pgsql
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd \
    && docker-php-ext-enable pdo pdo_pgsql pgsql \
    && php -m | grep pdo_pgsql || (echo '❌ pdo_pgsql not loaded!' && exit 1)

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
