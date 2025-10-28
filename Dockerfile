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

RUN npm install
RUN npm run build


# ==========================
# 🔹 ETAPA 2: BACKEND (Laravel + PHP 8.2 + PostgreSQL)
# ==========================
FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pgsql pdo mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-enable pdo_pgsql pgsql

WORKDIR /var/www/html

# Copiar código backend
COPY . .

# Copiar build do frontend
COPY --from=build-frontend /app/public/build ./public/build

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependências Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Criar .env se não existir
RUN php -r "if (!file_exists('.env')) copy('.env.example', '.env');"

# ✅ Garantir diretórios e permissões
RUN mkdir -p storage/app/public && \
    php artisan storage:link || true && \
    chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Gerar chave da aplicação
RUN php artisan key:generate || true

# Expor porta
EXPOSE 8000

# ==========================
# ✅ COMANDO FINAL DE STARTUP
# ==========================
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan migrate --force || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
