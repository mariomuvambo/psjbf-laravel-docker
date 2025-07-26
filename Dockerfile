# Usando a imagem oficial do PHP com FPM
FROM php:8.1-fpm

# Instalar dependências necessárias (como Composer e extensões PHP)
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    zip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copiar o código da aplicação para o container
COPY . /var/www/html

# Definir diretório de trabalho
WORKDIR /var/www/html

# Instalar dependências com o Composer
RUN composer install --no-dev --optimize-autoloader

# Ajustar permissões para diretórios de cache e logs do Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expor a porta do PHP-FPM
EXPOSE 9000

# Comando para rodar o Laravel (ajustado para o Render)
CMD ["php", "artisan", "serve", "--host", "0.0.0.0", "--port", "8000"]
