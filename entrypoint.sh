#!/bin/bash

# Detener el script si hay algún error
set -e

echo "🔧 Verificando dependencias de Composer..."
if [ ! -d "vendor" ]; then
  composer install
fi

echo "🧶 Verificando dependencias de Node.js..."
if [ ! -d "node_modules" ]; then
  npm install
fi

echo "🚀 Compilando assets con Vite..."
npm run dev &

# Iniciar PHP-FPM (necesario para que el contenedor no se detenga)
echo "🐘 Iniciando PHP-FPM..."
exec php-fpm
