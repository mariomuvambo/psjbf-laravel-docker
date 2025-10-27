# ==========================
# 🔹 ETAPA 1: BUILD FRONTEND (Vite)
# ==========================
FROM node:18 AS build-frontend
WORKDIR /app

# Copiar arquivos essenciais do frontend
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

# Instalar dependências e extensões necessárias do PHP
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pgsql pdo mbstring exif pcntl bcmath gd zip \
    && docker-php-ext-enable pdo_pgsql pgsql

# Confirmar extensões carregadas
RUN php -m | grep -E "pdo|pgsql" || (echo "❌ ERRO: extensões PDO/pgsql não carregadas!" && exit 1)

WORKDIR /var/www/html

# Copiar código do backend
COPY . .

# Copiar o build gerado do frontend
COPY --from=build-frontend /app/public/build ./public/build

# Instalar Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Instalar dependências do Laravel
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Criar .env se não existir
RUN php -r "if (!file_exists('.env')) copy('.env.example', '.env');"

# ✅ Criar link simbólico do storage (importante para fotos)
RUN php artisan storage:link || true

# Gerar chave da aplicação
RUN php artisan key:generate || true

# Corrigir permissões
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expor porta (Render usa PORT)
EXPOSE 8000

# ==========================
# ✅ COMANDO FINAL DE STARTUP
# ==========================
# No Render, esse CMD será executado a cada inicialização.
# Ele garante que o cache e o banco estejam sempre consistentes.
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan migrate --force || true && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
