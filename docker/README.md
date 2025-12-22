# Docker Restore Guide

## Prerequisites
- Ubuntu server with Docker and Docker Compose installed.
- Git installed.

## Steps
1. Clone the repo:
   git clone https://github.com/IgnacioRiquelme/Full_Sitio_Clip_20-12-2025.git
   cd Full_Sitio_Clip_20-12-2025

2. Copy and configure .env:
   cp .env.example .env
   # Edit .env with your actual values (DB credentials, etc.)

3. Build and start containers:
   cd docker
   docker-compose up -d --build

4. Restore database:
   docker exec -i laravel-mysql mysql -uroot -p'$MYSQL_ROOT_PASSWORD' < ../dumps/mysql_all_dump.sql

5. Rebuild Laravel app image if needed (since .tar was removed):
   docker build -t laravel-app:latest .

6. Access the app at http://your-server:8000

## Notes
- Volumes are not included; recreate them if needed.
- For production, adjust docker-compose.yml (e.g., add nginx reverse proxy).

