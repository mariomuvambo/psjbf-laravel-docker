# Etapa 1: imagem PHP com extensões e Composer
FROM php:8.2-fpm as php

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --optimize-autoloader
RUN chown -R www-data:www-data storage bootstrap/cache

# Etapa 2: Nginx + PHP juntos
FROM nginx:alpine

COPY --from=php /usr/local/etc/php /usr/local/etc/php
COPY --from=php /usr/local/bin/php /usr/local/bin/php
COPY --from=php /usr/bin/composer /usr/bin/composer
COPY --from=php /var/www/html /var/www/html

COPY nginx.conf /etc/nginx/conf.d/default.conf

WORKDIR /var/www/html

EXPOSE 80

CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]
