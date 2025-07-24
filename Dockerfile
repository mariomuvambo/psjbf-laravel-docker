FROM webdevops/php-nginx:8.3

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Permissões de pastas Laravel
RUN chown -R application:application /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# ✅ Permissão para o script de inicialização
RUN chmod +x /var/www/html/render-start.sh

# Comando para iniciar
CMD ["./render-start.sh"]
