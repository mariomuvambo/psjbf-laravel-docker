# Etapa 1: PHP com Laravel
FROM php:8.2-fpm

# Instala extensões necessárias
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

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos do projeto Laravel
COPY . .

# Instala dependências do Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões para storage e bootstrap/cache
RUN chown -R www-data:www-data storage bootstrap/cache

# Copia arquivos de configuração
COPY nginx.conf /etc/nginx/sites-available/default
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

# Comando para iniciar nginx + php-fpm via supervisor
CMD ["/usr/bin/supervisord"]
