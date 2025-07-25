# Usar uma imagem oficial do PHP com FPM
FROM php:8.1-fpm

# Instalar dependências necessárias
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install pdo pdo_mysql \
    && apt-get clean

# Instalar o Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Definir o diretório de trabalho
WORKDIR /var/www

# Copiar todos os arquivos do projeto para dentro do container
COPY . /var/www

# Instalar dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Expor a porta 80
EXPOSE 80

# Rodar o servidor PHP embutido (não recomendado para produção, mas para testes)
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
