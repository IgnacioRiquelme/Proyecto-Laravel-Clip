#!/bin/bash

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Verificar que se pasó un archivo como parámetro
if [ -z "$1" ]; then
    echo -e "${RED}❌ Debes especificar el archivo SQL a restaurar${NC}"
    echo ""
    echo -e "${BLUE}Uso: ./restore-database.sh <archivo.sql>${NC}"
    echo ""
    echo -e "${BLUE}Backups disponibles:${NC}"
    ls -lht ./backups/*.sql 2>/dev/null | head -5 | awk '{print "   " $9 " (" $5 ")"}'
    exit 1
fi

BACKUP_FILE="$1"

# Verificar que el archivo existe
if [ ! -f "$BACKUP_FILE" ]; then
    echo -e "${RED}❌ El archivo $BACKUP_FILE no existe${NC}"
    exit 1
fi

echo -e "${BLUE}🗄️  Iniciando restauración de base de datos...${NC}"
echo -e "${BLUE}📁 Archivo: $BACKUP_FILE${NC}"

# Verificar que el contenedor MySQL está corriendo
if ! docker ps | grep -q laravel-mysql; then
    echo -e "${RED}❌ El contenedor MySQL no está corriendo${NC}"
    echo "Ejecuta: docker-compose up -d"
    exit 1
fi

# Advertencia
echo ""
echo -e "${YELLOW}⚠️  ADVERTENCIA: Esto sobrescribirá la base de datos actual${NC}"
read -p "¿Estás seguro? (escribe 'si' para continuar): " confirmacion

if [ "$confirmacion" != "si" ]; then
    echo -e "${RED}❌ Restauración cancelada${NC}"
    exit 0
fi

# Restaurar el backup
echo -e "${BLUE}📦 Restaurando base de datos...${NC}"
docker exec -i laravel-mysql mysql -u laravel -psecret laravel < "$BACKUP_FILE"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Base de datos restaurada exitosamente!${NC}"
else
    echo -e "${RED}❌ Error al restaurar la base de datos${NC}"
    exit 1
fi
