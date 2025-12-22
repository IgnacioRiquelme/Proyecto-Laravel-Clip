# Guía de Restauración - Sitio Laravel con Docker

## Información del Respaldo

- **Fecha de creación**: 20 de diciembre de 2025
- **Archivo de respaldo**: `backup_Laravel_Full_2025-12-20_22-34-31.tar.gz`
- **Tamaño**: 553 MB
- **Respaldo de BD incluido**: `backups/backup_laravel_20251220_223346.sql` (678 KB)

## Requisitos Previos

Antes de restaurar, asegúrate de tener instalado en el servidor destino:

- **Docker** (versión 20.10 o superior)
- **Docker Compose** (versión 1.29 o superior)
- **Git** (opcional, para clonar el repo)

## Pasos de Restauración

### 1. Preparar el Entorno

```bash
# Crear directorio para el proyecto
mkdir laravel-docker
cd laravel-docker

# Si usas el respaldo desde GitHub, clona el repo
# git clone https://github.com/IgnacioRiquelme/Full_Sitio_Clip_20-12-2025.git .

# O copia el archivo backup_Laravel_Full_2025-12-20_22-34-31.tar.gz al directorio
# y descomprímelo
tar -xzf backup_Laravel_Full_2025-12-20_22-34-31.tar.gz
```

### 2. Verificar Docker

```bash
# Verificar que Docker esté corriendo
docker --version
docker-compose --version

# Si Docker no está corriendo, inicia el servicio
sudo systemctl start docker  # En Linux
# o equivalente en tu sistema operativo
```

### 3. Levantar los Contenedores Docker

```bash
# Desde el directorio raíz del proyecto (donde está docker-compose.yml)
docker-compose up -d

# Verificar que los contenedores estén corriendo
docker-compose ps
```

Deberías ver los siguientes contenedores activos:
- `laravel-app` (puerto 9000)
- `laravel-mysql` (puerto 3306)
- `laravel-nginx` (puerto 80 -> 8000)
- `phpmyadmin` (puerto 80 -> 8080)

### 4. Restaurar la Base de Datos

```bash
# Ejecutar el script de restauración
./restore-database.sh ./backups/backup_laravel_20251220_223346.sql
```

Este script:
- Verifica que el contenedor MySQL esté corriendo
- Importa el respaldo a la base de datos 'laravel'
- Usuario: laravel, Contraseña: secret

### 5. Instalar Dependencias (si es necesario)

```bash
# Instalar dependencias de PHP (Composer)
docker-compose exec app composer install

# Instalar dependencias de Node.js
npm install

# Compilar assets (si es necesario)
npm run build
```

### 6. Iniciar el Proyecto

```bash
# Ejecutar el script de inicio
./start-project.sh
```

Este script:
- Reinicia los contenedores Docker
- Espera a que MySQL esté listo
- Corrige permisos si es necesario
- Inicia Vite dev server

### 7. Verificar el Funcionamiento

- **Aplicación Laravel**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080
  - Usuario: laravel
  - Contraseña: secret
  - Base de datos: laravel

## Comandos Útiles

### Gestión de Contenedores
```bash
# Ver logs
docker-compose logs

# Detener contenedores
docker-compose down

# Reiniciar contenedores
docker-compose restart

# Acceder al contenedor de la app
docker-compose exec app bash
```

### Gestión de Base de Datos
```bash
# Backup manual
./backup-database.sh

# Restaurar backup específico
./restore-database.sh ./backups/nombre_del_archivo.sql

# Acceder a MySQL
docker-compose exec mysql mysql -u laravel -psecret laravel
```

### Desarrollo
```bash
# Ejecutar migraciones
docker-compose exec app php artisan migrate

# Limpiar cache
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear

# Ejecutar tests
docker-compose exec app php artisan test
```

## Configuración Adicional

### Variables de Entorno
El archivo `.env` ya está configurado en el respaldo. Si necesitas modificar:
- APP_URL
- DB_CONNECTION, DB_HOST, DB_DATABASE, etc.
- MAIL_* para configuración de email

### Permisos
Si hay problemas de permisos:
```bash
# Corregir permisos de storage y bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

## Solución de Problemas

### Contenedores no inician
```bash
# Verificar logs detallados
docker-compose logs

# Verificar puertos ocupados
netstat -tlnp | grep :8000
netstat -tlnp | grep :8080
netstat -tlnp | grep :3306
```

### Error de conexión a BD
- Verificar que el contenedor MySQL esté corriendo
- Revisar credenciales en `.env`
- Verificar que la BD se restauró correctamente

### Problemas con Vite/npm
```bash
# Limpiar node_modules
rm -rf node_modules package-lock.json
npm install

# Corregir permisos de .vite
sudo chown -R $USER:$USER node_modules/.vite
```

## Estructura del Proyecto

Después de la restauración, la estructura debería ser:
```
laravel-docker/
├── app/                    # Código de Laravel
├── backups/               # Respaldos de BD
├── config/                # Configuración de Laravel
├── database/              # Migraciones y seeds
├── docker-compose.yml     # Configuración de Docker
├── Dockerfile             # Imagen de Docker para la app
├── nginx/                 # Configuración de Nginx
├── mysql/                 # Configuración de MySQL
├── phpmyadmin-config/     # Configuración de phpMyAdmin
├── public/                # Archivos públicos
├── resources/             # Vistas, assets
├── routes/                # Definición de rutas
├── storage/               # Archivos temporales
├── tests/                 # Tests
├── vendor/                # Dependencias PHP
├── node_modules/          # Dependencias Node.js
├── composer.json          # Dependencias PHP
├── package.json           # Dependencias Node.js
├── start-project.sh       # Script de inicio
├── backup-database.sh     # Script de backup BD
├── restore-database.sh    # Script de restauración BD
└── RESTORE.md            # Esta guía
```

## Notas Importantes

- El respaldo incluye datos sensibles (credenciales de BD). Mantén seguro el archivo.
- Asegúrate de que los puertos 8000, 8080 y 3306 estén disponibles en el servidor destino.
- Si usas un dominio personalizado, actualiza APP_URL en .env y configura Nginx/Docker en consecuencia.
- Para producción, considera usar HTTPS y configurar variables de entorno seguras.

## Soporte

Si encuentras problemas durante la restauración, verifica:
1. Logs de Docker: `docker-compose logs`
2. Logs de Laravel: `docker-compose exec app tail -f storage/logs/laravel.log`
3. Permisos de archivos
4. Configuración de red/firewall