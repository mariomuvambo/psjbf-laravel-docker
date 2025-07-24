#!/usr/bin/env bash
echo "Rodando composer..."
composer install --no-dev --working-dir=/var/www/html

echo "Cacheando configurações..."
php artisan config:cache

echo "Cacheando rotas..."
php artisan route:cache

echo "Migrando banco de dados..."
php artisan migrate --force
