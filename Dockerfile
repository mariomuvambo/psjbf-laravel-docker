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

# Confirmar que as extensões foram realmente carregadas
RUN php -m | grep -E "pdo|pgsql" || (echo "❌ ERRO: extensões PDO/pgsql não carregadas!" && exit 1)

WORKDIR /var/www/html

# Copiar código fonte
COPY . .

# Copiar build do frontend
COPY --from=build-frontend /app/public/build ./public/build

# Instalar Composer e dependências Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Preparar ambiente Laravel
RUN php -r "if (!file_exists('.env')) copy('.env.example', '.env');"
RUN php artisan key:generate || true
RUN php artisan storage:link || true

# Corrigir permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000

# 🚀 No CMD (execução), limpas caches, migras e inicias o servidor
CMD php artisan config:clear && php artisan cache:clear && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
