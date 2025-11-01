# ==========================
# 🔹 ETAPA 1: BUILD FRONTEND (Vite)
# ==========================
FROM node:18 AS build-frontend
WORKDIR /app

# Copiar configs do Vite
COPY package*.json vite.config.js ./
ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}

# Copiar código e compilar build do Vite
COPY resources ./resources
COPY public ./public
RUN npm install && npm run build


# ==========================
# 🔹 ETAPA 2: BACKEND (Laravel + PHP 8.2 + PostgreSQL)
# ==========================
FROM php:8.2-fpm

# Instalar dependências do sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pgsql mbstring bcmath gd zip \
    && docker-php-ext-enable pdo_pgsql pgsql

# Configurações de upload e memória
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini

# Diretório de trabalho
WORKDIR /var/www/html

# Copiar código da aplicação
COPY . .

# Copiar build do frontend para o Laravel
COPY --from=build-frontend /app/public/build ./public/build

# ==========================
# 🔹 DEPENDÊNCIAS LARAVEL
# ==========================
# Instalar Composer e dependências otimizadas
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer clear-cache \
    && composer install --no-dev --optimize-autoloader --no-interaction

# ==========================
# 🔹 AJUSTES DE PERMISSÕES E CACHE
# ==========================
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache && \
    chown -R www-data:www-data storage bootstrap/cache && \
    php artisan storage:link || true && \
    php artisan key:generate || true && \
    composer dump-autoload -o && \
    php artisan optimize:clear && \
    php artisan config:cache && \
    php artisan route:cache

# Expor porta padrão HTTP
EXPOSE 8000

# ==========================
# 🔹 ENTRYPOINT PADRÃO (para Render/Docker Compose)
# ==========================
CMD php artisan migrate --force || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
