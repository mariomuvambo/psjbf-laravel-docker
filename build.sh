#!/usr/bin/env bash

echo "➡️ Build do frontend (Vite)"
cd resources/js
npm install
npm run build
cd ../..

echo "✅ Build frontend concluído"
