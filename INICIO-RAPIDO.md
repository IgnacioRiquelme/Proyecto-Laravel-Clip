# 🚀 Guía Rápida de Inicio del Proyecto

## 📝 Resumen del Problema

**Por qué `npm run build` no funciona correctamente:**
- `npm run build` compila los assets una sola vez para producción (sin hot-reload)
- Laravel necesita que Vite esté en modo desarrollo (`npm run dev`) para servir assets con hot-reload
- El error de permisos ocurre porque Docker a veces ejecuta comandos como root

---

## ✅ Solución Rápida - USAR SIEMPRE ESTO

### Opción 1: Script Automático (Recomendado)
```bash
cd ~/laravel-docker
./start-project.sh
```

Este script hace todo automáticamente:
- ✅ Levanta los contenedores Docker
- ✅ Corrige permisos automáticamente
- ✅ Inicia Vite en modo desarrollo
- ✅ Te muestra las URLs de acceso

### Opción 2: Comandos Manuales
```bash
cd ~/laravel-docker

# 1. Levantar contenedores
docker-compose up -d

# 2. Si tienes error de permisos, ejecuta esto UNA VEZ:
npm run fix-permissions

# 3. Iniciar Vite en desarrollo
npm run dev
```

---

## 🌐 URLs de Acceso

- **Aplicación Laravel:** http://localhost:8000
- **phpMyAdmin:** http://localhost:8080
  - Usuario: `laravel`
  - Contraseña: `secret`

---

## �� Detener el Proyecto

1. Presiona `Ctrl+C` para detener Vite
2. Ejecuta:
```bash
./stop-project.sh
# o manualmente:
docker-compose down
```

---

## ❌ NO USES ESTOS COMANDOS

- ❌ `npm run build` → Solo para producción, no para desarrollo
- ❌ Ejecutar comandos npm dentro del contenedor Docker sin corregir permisos

---

## 🔧 Solución al Error de Permisos (EACCES)

**Causa:** El contenedor Docker ejecuta comandos npm como root, creando archivos con permisos de root.

**Solución Permanente:**
```bash
npm run fix-permissions
```

O manualmente:
```bash
sudo chown -R $USER:$USER node_modules/.vite
```

---

## 📋 Comandos Disponibles

```bash
npm run dev              # Inicia Vite en desarrollo (USA ESTE)
npm run build            # Compila para producción
npm run start            # Ejecuta start-project.sh
npm run fix-permissions  # Corrige permisos de node_modules/.vite
```

---

## 💡 Tips

1. **Siempre usa `npm run dev`** para desarrollo, no `npm run build`
2. **Deja Vite corriendo** mientras trabajas en el proyecto
3. Si cierras y vuelves a abrir el proyecto, ejecuta `./start-project.sh`
4. Si ves error EACCES, ejecuta `npm run fix-permissions` y luego `npm run dev`

---

## 🐛 Troubleshooting

### Problema: "Error: EACCES: permission denied"
**Solución:** `npm run fix-permissions` y luego `npm run dev`

### Problema: "La página no tiene estilos CSS"
**Solución:** Asegúrate de que `npm run dev` esté corriendo (no `build`)

### Problema: "Cannot connect to MySQL"
**Solución:** `docker-compose up -d` y espera 5-10 segundos

### Problema: "Port 8000 already in use"
**Solución:** 
```bash
docker-compose down
docker-compose up -d
```


---

## 💾 Respaldo de Base de Datos

### Crear Respaldo (Backup)

Para respaldar toda la base de datos de phpMyAdmin:

```bash
cd ~/laravel-docker
./backup-database.sh
# o usando npm:
npm run backup
```

El backup se guardará en `./backups/backup_laravel_YYYYMMDD_HHMMSS.sql`

### Restaurar Respaldo

Para restaurar un backup anterior:

```bash
./restore-database.sh ./backups/backup_laravel_20251213_162809.sql
# o usando npm:
npm run restore ./backups/backup_laravel_20251213_162809.sql
```

**⚠️ ADVERTENCIA:** La restauración sobrescribirá la base de datos actual.

### Tips de Respaldo

1. **Haz backups antes de cambios importantes** en la BD
2. Los backups se guardan automáticamente en `./backups/`
3. El script muestra los últimos 5 backups disponibles
4. Puedes copiar los archivos `.sql` a otro lugar para mayor seguridad

