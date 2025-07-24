FROM webdevops/php-nginx:8.3

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar os arquivos da aplicação
COPY . .

# Instalar dependências do Composer
RUN composer install --no-dev --optimize-autoloader

# Dar permissões
RUN chown -R application:application /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache
