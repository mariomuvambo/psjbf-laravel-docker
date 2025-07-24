# Etapa 1: PHP com Laravel
FROM php:8.2-fpm as backend

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libonig-dev libxml2-dev zip unzip git curl libpq-dev \
    && docker-php-ext-install pdo_pgsql pdo_mysql mbstring exif pcntl bcmath gd

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define diretório de trabalho
WORKDIR /var/www/html

# Copia arquivos do projeto
COPY . .

# Instala dependências Laravel
RUN composer install --no-dev --optimize-autoloader

# Permissões
RUN chown -R www-data:www-data bootstrap/cache storage

# Copia configuração NGINX
COPY ./nginx.conf /etc/nginx/conf.d/default.conf

# Instala NGINX e Supervisor
RUN apt-get install -y nginx supervisor

# Adiciona arquivo do Supervisor
COPY ./supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
