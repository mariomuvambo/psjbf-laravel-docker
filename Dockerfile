FROM node:20-alpine as build-stage
WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build


FROM php:8.2-fpm-alpine as production-stage
WORKDIR /var/www/html


RUN apk add --no-cache \
    zip unzip git curl libpng-dev libjpeg-turbo-dev libwebp-dev libxpm-dev \
    oniguruma-dev icu-dev libzip-dev mysql-client bash postgresql-dev

RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer


COPY . .


COPY --from=build-stage /app/public/build ./public/build


RUN composer install --no-dev --optimize-autoloader


EXPOSE 8000


CMD php artisan key:generate && php artisan storage:link && php artisan serve --host=0.0.0.0 --port=8000
