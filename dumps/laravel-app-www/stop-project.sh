#!/bin/bash

echo "🛑 Deteniendo proyecto Laravel..."

# Detener Vite si está corriendo (Ctrl+C manual)
echo "ℹ️  Presiona Ctrl+C para detener Vite si está corriendo"

# Detener contenedores Docker
echo "📦 Deteniendo contenedores Docker..."
docker-compose down

echo "✅ Proyecto detenido correctamente"
