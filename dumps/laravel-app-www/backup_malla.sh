#!/bin/bash

# Ruta donde quieres guardar los backups
BACKUP_DIR="./backups"
TIMESTAMP=$(date +"%F_%H-%M")
FILENAME="backup_malla_${TIMESTAMP}.sql"

# Crear carpeta si no existe
mkdir -p "$BACKUP_DIR"

# Ejecutar respaldo desde el contenedor MySQL y guardarlo fuera
docker exec laravel-mysql mysqldump -ularavel -psecret laravel > "${BACKUP_DIR}/${FILENAME}"

# Mensaje de confirmación
if [ $? -eq 0 ]; then
    echo "✅ Respaldo completado: ${BACKUP_DIR}/${FILENAME}"
else
    echo "❌ Error al generar el respaldo"
fi

