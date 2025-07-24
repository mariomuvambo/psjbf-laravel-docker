# Etapa 1: PHP com Laravel
FROM php:8.2-fpm as backend

# Instala dependências e extensão pdo_pgsql
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev \
    libpq-dev zip unzip git curl \
    && docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copia o código Laravel
WORKDIR /var/www/html
COPY . .

# Instala dependências Laravel
RUN composer install --no-dev --optimize-autoloader

# Ajusta permissões
RUN chown -R www-data:www-data bootstrap/cache storage

# Etapa 2: NGINX como servidor web
FROM nginx:alpine

# Copia configuração do NGINX
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copia app Laravel da imagem backend
COPY --from=backend /var/www/html /var/www/html
COPY --from=backend /usr/bin/php /usr/bin/php

# Instala PHP e PHP-FPM + extensões mínimas
RUN apk add --no-cache php8 php8-fpm php8-mbstring php8-pdo_pgsql

# Define diretório de trabalho
WORKDIR /var/www/html

# Expõe a porta 80 para o Render detectar
EXPOSE 80

# Comando de inicialização do container
CMD ["sh", "-c", "php-fpm8 -D && nginx -g 'daemon off;'"]
