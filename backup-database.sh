#!/bin/bash

# Configuración
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
BACKUP_DIR="./backups"
BACKUP_FILE="backup_laravel_${TIMESTAMP}.sql"

# Colores para output
GREEN='\033[0;32m'
BLUE='\033[0;34m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${BLUE}🗄️  Iniciando respaldo de base de datos...${NC}"

# Crear directorio de backups si no existe
mkdir -p "$BACKUP_DIR"

# Verificar que el contenedor MySQL está corriendo
if ! docker ps | grep -q laravel-mysql; then
    echo -e "${RED}❌ El contenedor MySQL no está corriendo${NC}"
    echo "Ejecuta: docker-compose up -d"
    exit 1
fi

# Hacer el backup usando docker exec
echo -e "${BLUE}📦 Exportando base de datos 'laravel'...${NC}"
docker exec laravel-mysql mysqldump -u laravel -psecret laravel > "$BACKUP_DIR/$BACKUP_FILE"

# Verificar que el backup se creó correctamente
if [ -f "$BACKUP_DIR/$BACKUP_FILE" ]; then
    SIZE=$(du -h "$BACKUP_DIR/$BACKUP_FILE" | cut -f1)
    echo -e "${GREEN}✅ Backup creado exitosamente!${NC}"
    echo -e "${GREEN}📁 Archivo: $BACKUP_DIR/$BACKUP_FILE${NC}"
    echo -e "${GREEN}📊 Tamaño: $SIZE${NC}"
    
    # Listar últimos 5 backups
    echo ""
    echo -e "${BLUE}📋 Últimos backups disponibles:${NC}"
    ls -lht "$BACKUP_DIR"/*.sql 2>/dev/null | head -5 | awk '{print "   " $9 " (" $5 ")"}'
else
    echo -e "${RED}❌ Error al crear el backup${NC}"
    exit 1
fi

echo ""
echo -e "${GREEN}💡 Para restaurar este backup, usa:${NC}"
echo -e "   ${BLUE}./restore-database.sh $BACKUP_DIR/$BACKUP_FILE${NC}"
