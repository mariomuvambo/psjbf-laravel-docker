FROM node:18 AS build-frontend
WORKDIR /app

COPY package*.json ./
RUN npm install

COPY resources ./resources
COPY vite.config.js ./
COPY artisan ./
COPY composer.json composer.lock ./
COPY routes ./routes
COPY public ./public

RUN npm run build


FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd

WORKDIR /var/www/html
COPY . .


COPY --from=build-frontend /app/public/build ./public/build

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader

RUN php -r "if (!file_exists('.env')) copy('.env.example', '.env');"
RUN php artisan key:generate || true
RUN php artisan storage:link || true

RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 8000


CMD php artisan migrate --force || true && php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
