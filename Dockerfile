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
# 🔹 BUILD BACKEND
# ==========================
FROM php:8.2-fpm

# Instala dependências necessárias
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_pgsql pgsql pdo mbstring exif pcntl bcmath gd

# Ativa as extensões PHP tanto no FPM quanto no CLI
RUN echo "extension=pdo_pgsql.so" > /usr/local/etc/php/conf.d/docker-php-ext-pdo_pgsql.ini \
    && echo "extension=pgsql.so" > /usr/local/etc/php/conf.d/docker-php-ext-pgsql.ini

# Confirma se o driver está ativo
RUN php -m | grep -E "pdo_pgsql|pgsql" || (echo "❌ PostgreSQL drivers not loaded!" && exit 1)

WORKDIR /var/www/html

COPY . .

# Copia o build do frontend
COPY --from=build-frontend /app/public/build ./public/build

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Gera .env e APP_KEY
RUN php -r "if (!file_exists('.env')) copy('.env.example', '.env');"
RUN php artisan key:generate || true
RUN php artisan storage:link || true

# Permissões corretas
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

# Verifica se o driver está ativo no runtime antes de iniciar o servidor
CMD php -m | grep pdo_pgsql || (echo '❌ pdo_pgsql not loaded at runtime' && exit 1); \
    php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
