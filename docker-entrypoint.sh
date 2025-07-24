#!/bin/bash

# Instala dependências
composer install --no-dev --optimize-autoloader

# Permissões
php artisan config:cache
php artisan route:cache
php artisan migrate --force

# Inicia o servidor (da imagem oficial)
exec /opt/docker/bin/entrypoint supervisord
