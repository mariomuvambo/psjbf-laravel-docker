# Etapa 1: PHP com Laravel
FROM php:8.2-fpm as backend

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev zip unzip git curl \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data bootstrap/cache storage

# Etapa 2: NGINX como servidor web
FROM nginx:alpine

# Copia o nginx.conf
COPY nginx.conf /etc/nginx/conf.d/default.conf

# Copia o app Laravel da imagem backend
COPY --from=backend /var/www/html /var/www/html
COPY --from=backend /usr/bin/php /usr/bin/php

# Instala PHP-FPM para rodar com NGINX
RUN apk add --no-cache php8 php8-fpm php8-mbstring php8-pdo_mysql

WORKDIR /var/www/html

EXPOSE 80

CMD ["sh", "-c", "php-fpm8 -D && nginx -g 'daemon off;'"]
