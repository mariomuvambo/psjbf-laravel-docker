# ==========================
# 🔹 ETAPA 1: BUILD FRONTEND (Vite)
# ==========================
FROM node:18 AS build-frontend
WORKDIR /app

# Copiar configs do Vite
COPY package*.json vite.config.js ./
ARG VITE_API_URL
ENV VITE_API_URL=${VITE_API_URL}

# Copiar o frontend e compilar
COPY resources ./resources
COPY public ./public
RUN npm install && npm run build


# ==========================
# 🔹 ETAPA 2: BACKEND (Laravel + PHP 8.2 + PostgreSQL)
# ==========================
FROM php:8.2-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev libzip-dev \
    && docker-php-ext-install pdo_pgsql pgsql mbstring bcmath gd zip \
    && docker-php-ext-enable pdo_pgsql pgsql

# Ajustes de upload e memória
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size=10M" >> /usr/local/etc/php/conf.d/uploads.ini

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar código da aplicação
COPY . .

# Copiar build do frontend
COPY --from=build-frontend /app/public/build ./public/build

# Instalar Composer e dependências do Laravel
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Criar storage e permissões
RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && php artisan storage:link || true \
    && chown -R www-data:www-data storage bootstrap/cache

# Gerar chave da aplicação (ignorar se já existir)
RUN php artisan key:generate || true

# Expor porta HTTP
EXPOSE 8000

# ==========================
# 🔹 ENTRYPOINT PADRÃO (usado pelo serviço web)
# ==========================
CMD php artisan config:clear && \
    php artisan cache:clear && \
    php artisan config:cache && \
    php artisan migrate --force || true && \
    php artisan storage:link && \
    php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
