FROM php:8.2-fpm

# Instala extensões e ferramentas
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório do Laravel
WORKDIR /var/www/html

# Copia projeto
COPY . .

# Instala dependências
RUN composer install --no-dev --optimize-autoloader
RUN cp .env.example .env && php artisan key:generate


# Permissões
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Gera .env e key
RUN cp .env.example .env && php artisan key:generate

# Copia configs nginx e supervisord
COPY nginx.conf /etc/nginx/conf.d/default.conf

COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Exponha porta
EXPOSE 80

# Start services
CMD ["/usr/bin/supervisord"]
