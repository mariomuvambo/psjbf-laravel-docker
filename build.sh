#!/usr/bin/env bash

# Aborta se algum comando falhar
set -e

# Instala dependências PHP
composer install --no-interaction --prefer-dist --optimize-autoloader

# Instala dependências do Node e compila o frontend com Vite
echo "➡️ Build do frontend (Vite)"
cd resources/js
npm install
npm run build
cd ../..

# Gera cache e outras otimizações do Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "✅ Build frontend concluído"
