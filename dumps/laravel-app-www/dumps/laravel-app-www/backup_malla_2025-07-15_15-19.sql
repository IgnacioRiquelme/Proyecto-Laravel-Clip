-- MySQL dump 10.13  Distrib 8.0.42, for Linux (x86_64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	8.0.42

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados_procesos`
--

DROP TABLE IF EXISTS `estados_procesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados_procesos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `color_fondo` varchar(20) DEFAULT NULL,
  `color_texto` varchar(20) DEFAULT NULL,
  `borde_color` varchar(20) DEFAULT NULL,
  `emoji` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_procesos`
--

LOCK TABLES `estados_procesos` WRITE;
/*!40000 ALTER TABLE `estados_procesos` DISABLE KEYS */;
INSERT INTO `estados_procesos` VALUES (1,'Pendiente','#f3f4f6','#000000','#d1d5db','🕒',NULL,NULL),(2,'En ejecución','#2563eb','#ffffff','#1e40af','⚙️',NULL,NULL),(3,'Ok','#10b981','#ffffff','#065f46','✅',NULL,NULL),(4,'Anómalo','#f59e0b','#ffffff','#b45309','⚠️',NULL,NULL),(5,'No corre','#6b7280','#ffffff','#374151','🚫',NULL,NULL),(6,'Undurraga','#8b5cf6','#ffffff','#6d28d9','🧪',NULL,NULL),(7,'OK con observaciones','#3b82f6','#ffffff','#1e3a8a','🔍',NULL,NULL),(8,'Sin Registro','#d1d5db','#000000','#9ca3af','❔',NULL,NULL);
/*!40000 ALTER TABLE `estados_procesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupos`
--

DROP TABLE IF EXISTS `grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `color` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupos`
--

LOCK TABLES `grupos` WRITE;
/*!40000 ALTER TABLE `grupos` DISABLE KEYS */;
INSERT INTO `grupos` VALUES (1,'Procesos Críticos','red',NULL,NULL),(2,'Proc. Generales | Ambiente Concorde','indigo',NULL,NULL),(3,'Proc. Generales | Ambiente AS/400 P06','blue',NULL,NULL),(4,'PROCESO VIDA','green',NULL,NULL),(5,'Procesos Web | Proceso Carga Masiva de Kia (después de las 22 y antes de las 00 hrs)','purple',NULL,NULL),(6,'Procesos Zenit','yellow',NULL,NULL),(7,'Proc. Generales','gray',NULL,NULL),(8,'Procesos Web','orange',NULL,NULL),(9,'Revisión MIMIX SHARE','slate',NULL,NULL),(10,'Revisión Enforcive','slate',NULL,NULL);
/*!40000 ALTER TABLE `grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_05_06_184330_add_area_to_users_table',1),(5,'2025_05_06_194505_add_two_factor_columns_to_users_table',1),(6,'2025_05_15_173802_create_requerimientos_table',1),(7,'2025_05_18_181951_add_creado_por_to_requerimientos_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `nombres_procesos`
--

DROP TABLE IF EXISTS `nombres_procesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `nombres_procesos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_proceso` varchar(10) DEFAULT NULL,
  `grupo` varchar(100) NOT NULL,
  `proceso` varchar(200) NOT NULL,
  `descripcion` text,
  `hora_programada` time DEFAULT NULL,
  `dias` json DEFAULT (json_array()),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_proceso` (`id_proceso`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `nombres_procesos`
--

LOCK TABLES `nombres_procesos` WRITE;
/*!40000 ALTER TABLE `nombres_procesos` DISABLE KEYS */;
INSERT INTO `nombres_procesos` VALUES (1,'P159','Procesos Críticos','Carga de Moneda (Concorde)','MONEDA','21:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(2,'P160','Procesos Críticos','Carga de Moneda (Breton)','MONEDA','06:15:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(3,'P162','Procesos Críticos','Reinicio de aplicaciones DPS','BCI-SERDPS / DPS_BANCO / WSDPS','11:45:00','[\"friday\"]',NULL,NULL),(4,'P206','Proc. Generales | Ambiente Concorde','SU.INFPRO','CONCORDE','19:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"sunday\"]',NULL,NULL),(5,'P205','Proc. Generales | Ambiente Concorde','GESTION BANCASEGUROS - GEXBS02','Ejecutar Proceso en menú de operador, Proceso Solicitado por Luis Tapia','14:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\"]',NULL,NULL),(6,'P029','Proc. Generales | Ambiente Concorde','Pólizas de Renovaciones Vida','POLIZAS DE RENOVACIONES VIDA TODOS LOS LUNES 7:00','07:00:00','[\"sunday\"]',NULL,NULL),(7,'P126','Proc. Generales | Ambiente Concorde','DE_SELECC1','Seleccionar y Actualizar Deudas Atrasadas, y efectuar Cancelaciones de Pólizas Ejecutar antes de las 24:00 hrs ','23:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\"]',NULL,NULL),(8,'P124','Proc. Generales | Ambiente Concorde','GESP101','Genera Producción para Gestión ','20:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\"]',NULL,NULL),(9,'P125','Proc. Generales | Ambiente Concorde','GESP109','Genera Siniestros para Gestión ','20:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\"]',NULL,NULL),(10,'P130','Proc. Generales | Ambiente Concorde','Data Ware House Producción','Proceso diario Producción TIRAR DESPUES DE CANCELACIONES','22:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\"]',NULL,NULL),(11,'P158','Proc. Generales | Ambiente Concorde','Data Ware House Siniestro','Proceso diario DW SINIESTROS','22:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\"]',NULL,NULL),(12,'P163','Proc. Generales | Ambiente Concorde','Proceso DWH de transferencia y carga al Oracle','Proceso de Carga Data Mart Después del DWH Diario','23:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\"]',NULL,NULL),(13,'P215','Proc. Generales | Ambiente Concorde','DE_INFORMS','Emitir Informe y Carta 025 ','18:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(14,'P166','Proc. Generales | Ambiente Concorde','Proceso SN.G35A','Nueva versión panel de liquidadores. Se ejecuta a las 5:00 hrs de lunes a Viernes 1:31 hrs','05:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(15,'P183','Proc. Generales | Ambiente Concorde','Proceso  WEB - BCI ','Proceso para el Banco WEB - BCI','23:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"sunday\"]',NULL,NULL),(16,'P119','Proc. Generales | Ambiente Concorde','RESPALDO Concorde ','RESPALDO EQUIPO CONCORDE AS400 LUNES A DOMINGO DIARIO','23:45:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(17,'P120','Proc. Generales | Ambiente Concorde','RESPALDO Concorde ','RESPALDO EQUIPO CONCORDE AS400 RESPALDO MENSUAL/CONTABLE','23:59:00','[\"second saturday of month\"]',NULL,NULL),(18,'P140','Proc. Generales | Ambiente Concorde','RESPALDO Elkan','RESPALDO EQUIPO ELKAN AS400 DESARROLLO','22:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(19,'P216','Proc. Generales | Ambiente AS/400 P06','(CONCORDE) PROPUESTAS WEB AS400','Origen AS400','23:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(20,'P217','Proc. Generales | Ambiente AS/400 P06','(P06) PROPUESTAS WEB - TEMPORALES ','Temporales','23:15:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(21,'P218','Proc. Generales | Ambiente AS/400 P06','(P06) PROPUESTAS WEB - CARGA WEB ','Web Oracle','23:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(22,'P219','Proc. Generales | Ambiente AS/400 P06','(CONCORDE) CANCELACIONES WEB ','Origen AS400','23:45:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(23,'P220','Proc. Generales | Ambiente AS/400 P06','(P06) Cancelaciones Generales Temporales','Temporales','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(24,'P221','Proc. Generales | Ambiente AS/400 P06','(P06) Cancelaciones Generales Web Oracle','Web Oracle','00:15:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(25,'P104','Proc. Generales | Ambiente AS/400 P06','(CONCORDE) Producción Generales Origen AS400','Origen AS400 ','00:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(26,'P105','Proc. Generales | Ambiente AS/400 P06','(P06) Producción Generales Temporales','Temporales','00:45:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(27,'P106','Proc. Generales | Ambiente AS/400 P06','(P06) Producción Generales Web Oracle','Web Oracle','01:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(28,'P101','Proc. Generales | Ambiente AS/400 P06','(CONCORDE) Plan Pago Generales Origen AS400','Origen AS400','01:15:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(29,'P102','Proc. Generales | Ambiente AS/400 P06','(P06) Plan Pago Generales Temporales','Temporales','01:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(30,'P103','Proc. Generales | Ambiente AS/400 P06','(P06) Plan Pago Generales Web Oracle','Web Oracle','01:45:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(31,'P108','PROCESO VIDA','(P01) Producción Vida Origen','Origen Oracle','02:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(32,'P109','PROCESO VIDA','(P06) Producción Vida Temporales','Temporales','02:15:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(33,'P110','PROCESO VIDA','(P06) Producción Vida Web Oracle','Web Oracle','02:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(34,'P111','PROCESO VIDA','(P01) Plan Pago Vida Origen','Origen Oracle','02:45:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(35,'P112','PROCESO VIDA','(P06) Plan Pago Vida Temporales','Temporales','03:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(36,'P113','PROCESO VIDA','(P06) Plan Pago Vida Web Oracle','Web Oracle','03:15:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(37,'P049','Procesos Web | Proceso Carga Masiva de Kia (después de las 22 y antes de las 00 hrs)','Proceso Kia web y AS400','Carga Pólizas Cotizador Web Ejecutar a las 12:00 hrs (SE PROCESA DE LUNES A VIERNES A EXCEPCIÓN DEL MARTES)','12:00:00','[\"monday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(38,'P059','Procesos Web | Proceso Carga Masiva de Kia (después de las 22 y antes de las 00 hrs)','Proceso Kia web y AS400','Carga Pólizas Cotizador Web Ejecutar a las 15:00 hrs (SE PROCESA DE LUNES A VIERNES)','15:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(39,'P087','Procesos Web | Proceso Carga Masiva de Kia (después de las 22 y antes de las 00 hrs)','Proceso Kia web y AS400','Carga Pólizas Cotizador Web Ejecutar a las 17:00 hrs (SE PROCESA DE LUNES A VIERNES)','17:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(40,'P097','Procesos Web | Proceso Carga Masiva de Kia (después de las 22 y antes de las 00 hrs)','Proceso Kia web y AS400','Carga Pólizas Cotizador Web Ejecutar a las 19:00 hrs (SE PROCESA DE LUNES A VIERNES)','19:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(41,'P134','Procesos Web | Proceso Carga Masiva de Kia (después de las 22 y antes de las 00 hrs)','Proceso Kia web y AS400','Carga Pólizas Cotizador Web Ejecutar a las 21:45 hrs (SE PROCESA DE LUNES A VIERNES)','21:45:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(42,'P161','Procesos Web | Proceso Carga Masiva de Kia (después de las 22 y antes de las 00 hrs)','Envío Supwi01 a Teams','Enviar Archivos Generados SUPWI01 a Carpeta Teams','23:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(43,'P209','COMPAÑÍA ZENIT SEGUROS','SU.INFPRO','BRETON - ZENIT','19:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"sunday\"]',NULL,NULL),(44,'P107','COMPAÑÍA ZENIT SEGUROS','DEPCC18','Actualizar los avisos de Vencimiento Depcc18','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(45,'P114','COMPAÑÍA ZENIT SEGUROS','DE_SELECC1','ACTUALIZAR DEUDAS ATRASADAS De. Selecc1 (Automático) 20:30','20:30:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(46,'P115','COMPAÑÍA ZENIT SEGUROS','GESP101','Gene de Inf de Gestión utilizada (Automático)','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(47,'P116','COMPAÑÍA ZENIT SEGUROS','GESP109','Gene de Inf de Gestión utilizada (Automático)','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(48,'P171','COMPAÑÍA ZENIT SEGUROS','DE_INFORMS','Info y Carta y comprobante de pagos 025 De. Informs (Automático) 01:00 PM','13:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(49,'P129','COMPAÑÍA ZENIT SEGUROS','Data Ware House','Proceso diario Producción','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\"]',NULL,NULL),(50,'P121','COMPAÑÍA ZENIT SEGUROS','Proc 601','Ts. PCB601X ZENIT (liberar)','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(51,'P122','COMPAÑÍA ZENIT SEGUROS','Proc 602','Ts. PCB602X ZENIT (liberar)','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(52,'P123','COMPAÑÍA ZENIT SEGUROS','Proc 603','Ts. PCB603X ZENIT (liberar)','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(53,'P167','COMPAÑÍA ZENIT SEGUROS','Proceso SN.G35A','Nueva versión panel de liquidadores se ejecuta a las 05:00 hrs de lunes a Viernes','05:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"sunday\"]',NULL,NULL),(54,'P127','COMPAÑÍA ZENIT SEGUROS','RESPALDO','RESPALDO EQUIPO BRETON AS400 LUNES A DOMINGO DIARIO','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(55,'P128','COMPAÑÍA ZENIT SEGUROS','RESPALDO','RESPALDO EQUIPO BRETON AS400 RESPALDO MENSUAL/ CONTABLE','00:00:00','[\"saturday\"]',NULL,NULL),(56,'P172','Proc. Generales','(BRETON) Producción Zenit','Ejecución automática Schedulix','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(57,'P173','Proc. Generales','(P06) CARGA PRODUCCION GENE TMP','Ejecución automática Schedulix','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(58,'P174','Proc. Generales','(P06) CARGA PRODUCCION GENE','Ejecución automática Schedulix','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(59,'P177','Proc. Generales','(BRETON) Plan pagos Zenit','Ejecución automática Schedulix','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(60,'P178','Proc. Generales','(P06) CARGA PLANPAGO GENE TMP','Ejecución automática Schedulix','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(61,'P179','Proc. Generales','(P06) CARGA PLANPAGO GENE','Ejecución automática Schedulix','00:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(62,'P154','Procesos Web','Proceso Kia web y AS400','Carga Pólizas Cotizador Web Ejecutar a las 22:00 hrs (SE PROCESA DE LUNES A VIERNES)','22:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\"]',NULL,NULL),(63,'P003','Revisión MIMIX SHARE','MIMIX SHARE (MONCON) 8:00','MIMIX SHARE','08:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(64,'P131','Revisión MIMIX SHARE','MIMIX SHARE (MONCON) 15:00','MIMIX SHARE','15:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(65,'P139','Revisión MIMIX SHARE','MIMIX SHARE (MONCON) 21:00','MIMIX SHARE','21:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL),(66,'P180','Revisión Enforcive','Revisión Enforcive 02:00','Enforcive','02:00:00','[\"monday\", \"tuesday\", \"wednesday\", \"thursday\", \"friday\", \"saturday\", \"sunday\"]',NULL,NULL);
/*!40000 ALTER TABLE `nombres_procesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `operadores`
--

DROP TABLE IF EXISTS `operadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `operadores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `correo` varchar(100) DEFAULT NULL,
  `sigla` varchar(10) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `correo` (`correo`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `operadores`
--

LOCK TABLES `operadores` WRITE;
/*!40000 ALTER TABLE `operadores` DISABLE KEYS */;
INSERT INTO `operadores` VALUES (1,'eric.peralta@cliptecnologia.com','EPG','Eric Peralta',NULL,NULL),(2,'pablo.diaz@cliptecnologia.com','PD','Pablo Díaz',NULL,NULL),(3,'mauricio.barrera@cliptecnologia.com','MB','Mauricio Barrera',NULL,NULL),(4,'Eukaris.riera@cliptecnologia.com','ER','Eukaris Riera',NULL,NULL),(5,'diego.perez@cliptecnologia.com','DP','Diego Pérez',NULL,NULL),(6,'itham.espinosa@cliptecnologia.com','IE','Itham Espinosa',NULL,NULL),(7,'alvaro.feo@cliptecnologia.com','AF','Alvaro Feo',NULL,NULL),(8,'ignacio.riquelme@cliptecnologia.com','IR','Ignacio Riquelme',NULL,NULL),(9,'rene.rivera@cliptecnologia.com','RR','Rene Rivera',NULL,NULL),(10,'cristian.larrondo@cliptecnologia.com','CL','Cristian Larrondo',NULL,NULL);
/*!40000 ALTER TABLE `operadores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `procesos`
--

DROP TABLE IF EXISTS `procesos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `procesos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_proceso` varchar(10) DEFAULT NULL,
  `grupo` varchar(100) NOT NULL,
  `inicio` datetime DEFAULT NULL,
  `fin` datetime DEFAULT NULL,
  `total` time DEFAULT NULL,
  `adm_inicio` varchar(50) DEFAULT NULL,
  `adm_fin` varchar(50) DEFAULT NULL,
  `correo_inicio` varchar(100) DEFAULT NULL,
  `correo_fin` varchar(100) DEFAULT NULL,
  `estado_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_estado` (`estado_id`),
  KEY `fk_proceso_nombre` (`id_proceso`),
  CONSTRAINT `fk_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_procesos` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_proceso_nombre` FOREIGN KEY (`id_proceso`) REFERENCES `nombres_procesos` (`id_proceso`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `procesos`
--

LOCK TABLES `procesos` WRITE;
/*!40000 ALTER TABLE `procesos` DISABLE KEYS */;
INSERT INTO `procesos` VALUES (1,'P159','Procesos Críticos','2025-07-15 05:13:00','2025-07-15 15:12:00','09:59:00','IR','IR','ignacio.riquelme@cliptecnologia.com','ignacio.riquelme@cliptecnologia.com',3,'2025-07-15 19:13:37','2025-07-15 19:14:00'),(2,'P160','Procesos Críticos','2025-07-14 16:15:00','2025-07-15 01:20:00','09:05:00','IR','IR','ignacio.riquelme@cliptecnologia.com','ignacio.riquelme@cliptecnologia.com',3,'2025-07-15 19:15:49','2025-07-15 19:15:49'),(3,'P206','Proc. Generales | Ambiente Concorde','2025-07-14 13:22:00','2025-07-14 15:16:00','01:54:00','IR','IR','ignacio.riquelme@cliptecnologia.com','ignacio.riquelme@cliptecnologia.com',3,'2025-07-15 19:16:29','2025-07-15 19:16:29');
/*!40000 ALTER TABLE `procesos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requerimientos`
--

DROP TABLE IF EXISTS `requerimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `requerimientos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fecha_hora` datetime NOT NULL,
  `turno` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `numero_ticket` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `requerimiento` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `solicitante` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `negocio` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ambiente` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `servidor` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_solicitud` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_pase` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `creado_por` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `requerimientos_numero_ticket_unique` (`numero_ticket`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requerimientos`
--

LOCK TABLES `requerimientos` WRITE;
/*!40000 ALTER TABLE `requerimientos` DISABLE KEYS */;
INSERT INTO `requerimientos` VALUES (1,'2025-07-15 07:46:05','noche','REQ 2025-036599','Pase a QA','Alain Diaz','BCI Seguros','As400','Aplicativo','Ascerbci','Exitoso','Proactivanet','Normal','N/A','Sin observaciones','Ignacio Riquelme',NULL,NULL);
/*!40000 ALTER TABLE `requerimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('m7k0Dtr5V0YhsVYiaKMAtCUkyVCyQAtX0vqrMs88',1,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiMGZadGd1eTNlTU01SXRlaUtxNE9FV29EWEVTZ2ZiQjZ5S1dPaVFDeSI7czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTtzOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czozNjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL3Byb2Nlc29zL21hbGxhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1752606989),('RhMDywtfOto5COq3y0WsTRG2s8bhFegbemSvqAfa',1,'172.18.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiSXdITmM5QnJ4a09adFN6c2htRENPcFVjREN3b2NkYWJweDVSNTVwMSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNjoiaHR0cDovL2xvY2FsaG9zdDo4MDAwL3Byb2Nlc29zL21hbGxhIjt9czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDM6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9yZXF1ZXJpbWllbnRvcy9jcmVhdGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=',1752580021);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_ambientes`
--

DROP TABLE IF EXISTS `tipos_ambientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_ambientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_ambientes`
--

LOCK TABLES `tipos_ambientes` WRITE;
/*!40000 ALTER TABLE `tipos_ambientes` DISABLE KEYS */;
INSERT INTO `tipos_ambientes` VALUES (1,'As400','2025-07-15 11:40:27','2025-07-15 11:40:27'),(2,'Web','2025-07-15 11:40:27','2025-07-15 11:40:27'),(3,'Local','2025-07-15 11:40:27','2025-07-15 11:40:27'),(4,'SQL','2025-07-15 11:40:27','2025-07-15 11:40:27');
/*!40000 ALTER TABLE `tipos_ambientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_capas`
--

DROP TABLE IF EXISTS `tipos_capas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_capas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_capas`
--

LOCK TABLES `tipos_capas` WRITE;
/*!40000 ALTER TABLE `tipos_capas` DISABLE KEYS */;
INSERT INTO `tipos_capas` VALUES (1,'Aplicativo','2025-07-15 11:40:55','2025-07-15 11:40:55'),(2,'Otros','2025-07-15 11:40:55','2025-07-15 11:40:55'),(3,'Carga Web','2025-07-15 11:40:55','2025-07-15 11:40:55'),(4,'Control de versiones','2025-07-15 11:40:55','2025-07-15 11:40:55'),(5,'DB2','2025-07-15 11:40:55','2025-07-15 11:40:55'),(6,'Infraestructura','2025-07-15 11:40:55','2025-07-15 11:40:55'),(7,'Replica','2025-07-15 11:40:55','2025-07-15 11:40:55'),(8,'Sistema Operativo','2025-07-15 11:40:55','2025-07-15 11:40:55'),(9,'Base de datos','2025-07-15 11:40:55','2025-07-15 11:40:55');
/*!40000 ALTER TABLE `tipos_capas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_estados`
--

DROP TABLE IF EXISTS `tipos_estados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_estados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_estados`
--

LOCK TABLES `tipos_estados` WRITE;
/*!40000 ALTER TABLE `tipos_estados` DISABLE KEYS */;
INSERT INTO `tipos_estados` VALUES (1,'Exitoso','2025-07-15 11:41:46','2025-07-15 11:41:46'),(2,'Rechazado','2025-07-15 11:41:46','2025-07-15 11:41:46'),(3,'En curso','2025-07-15 11:41:46','2025-07-15 11:41:46'),(4,'Erroneo','2025-07-15 11:41:46','2025-07-15 11:41:46'),(5,'Pendiente','2025-07-15 11:41:46','2025-07-15 11:41:46');
/*!40000 ALTER TABLE `tipos_estados` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_ics`
--

DROP TABLE IF EXISTS `tipos_ics`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_ics` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_ics`
--

LOCK TABLES `tipos_ics` WRITE;
/*!40000 ALTER TABLE `tipos_ics` DISABLE KEYS */;
INSERT INTO `tipos_ics` VALUES (1,'N/A','2025-07-15 11:42:07','2025-07-15 11:42:07'),(2,'Si','2025-07-15 11:42:07','2025-07-15 11:42:07'),(3,'No','2025-07-15 11:42:07','2025-07-15 11:42:07');
/*!40000 ALTER TABLE `tipos_ics` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_negocios`
--

DROP TABLE IF EXISTS `tipos_negocios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_negocios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_negocios`
--

LOCK TABLES `tipos_negocios` WRITE;
/*!40000 ALTER TABLE `tipos_negocios` DISABLE KEYS */;
INSERT INTO `tipos_negocios` VALUES (1,'ZENIT Seguros','2025-07-15 11:40:07','2025-07-15 11:40:07'),(2,'BCI Seguros','2025-07-15 11:40:07','2025-07-15 11:40:07'),(3,'Vida Seguros','2025-07-15 11:40:07','2025-07-15 11:40:07');
/*!40000 ALTER TABLE `tipos_negocios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_pases`
--

DROP TABLE IF EXISTS `tipos_pases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_pases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_pases`
--

LOCK TABLES `tipos_pases` WRITE;
/*!40000 ALTER TABLE `tipos_pases` DISABLE KEYS */;
INSERT INTO `tipos_pases` VALUES (1,'Emergencia','2025-07-15 11:42:07','2025-07-15 11:42:07'),(2,'Normal','2025-07-15 11:42:07','2025-07-15 11:42:07'),(3,'Rollback','2025-07-15 11:42:07','2025-07-15 11:42:07');
/*!40000 ALTER TABLE `tipos_pases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_requerimientos`
--

DROP TABLE IF EXISTS `tipos_requerimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_requerimientos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_requerimientos`
--

LOCK TABLES `tipos_requerimientos` WRITE;
/*!40000 ALTER TABLE `tipos_requerimientos` DISABLE KEYS */;
INSERT INTO `tipos_requerimientos` VALUES (1,'Pase a Producción','2025-07-15 11:39:43','2025-07-15 11:39:43'),(2,'Pase a QA','2025-07-15 11:39:43','2025-07-15 11:39:43'),(3,'Creación de usuario','2025-07-15 11:39:43','2025-07-15 11:39:43'),(4,'Desvinculación de Usuario','2025-07-15 11:39:43','2025-07-15 11:39:43'),(5,'Cambio de Perfil','2025-07-15 11:39:43','2025-07-15 11:39:43'),(6,'Cambio colas de Trabajo','2025-07-15 11:39:43','2025-07-15 11:39:43'),(7,'Incorporacion de Servidor en Nagios','2025-07-15 11:39:43','2025-07-15 11:39:43'),(8,'Comisiones Vida','2025-07-15 11:39:43','2025-07-15 11:39:43'),(9,'Cierre Mensual DWH','2025-07-15 11:39:43','2025-07-15 11:39:43'),(10,'Comisiones Generales','2025-07-15 11:39:43','2025-07-15 11:39:43'),(11,'Ejecucion de Script','2025-07-15 11:39:43','2025-07-15 11:39:43'),(12,'Restauracion de Cinta','2025-07-15 11:39:43','2025-07-15 11:39:43'),(13,'CIERRE COASEGURO','2025-07-15 11:39:43','2025-07-15 11:39:43'),(14,'Descargar cartuchos TSM','2025-07-15 11:39:43','2025-07-15 11:39:43'),(15,'Proceso FECU','2025-07-15 11:39:43','2025-07-15 11:39:43'),(16,'Cambio de Contraseña AS400','2025-07-15 11:39:43','2025-07-15 11:39:43'),(17,'Habilitación de Usuarios AS400','2025-07-15 11:39:43','2025-07-15 11:39:43'),(18,'Habilitación de Pantalla','2025-07-15 11:39:43','2025-07-15 11:39:43'),(19,'CIERRE CTA.CTE.PRODUCTORES','2025-07-15 11:39:43','2025-07-15 11:39:43'),(20,'Código fuente','2025-07-15 11:39:43','2025-07-15 11:39:43'),(21,'Cierre Producción Mensual Generales','2025-07-15 11:39:43','2025-07-15 11:39:43'),(22,'WEB(GitHub)','2025-07-15 11:39:43','2025-07-15 11:39:43'),(23,'Cambio de reporte de una impresora a otra','2025-07-15 11:39:43','2025-07-15 11:39:43'),(24,'Consola','2025-07-15 11:39:43','2025-07-15 11:39:43');
/*!40000 ALTER TABLE `tipos_requerimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_servidores`
--

DROP TABLE IF EXISTS `tipos_servidores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_servidores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_servidores`
--

LOCK TABLES `tipos_servidores` WRITE;
/*!40000 ALTER TABLE `tipos_servidores` DISABLE KEYS */;
INSERT INTO `tipos_servidores` VALUES (1,'Breton','2025-07-15 11:41:15','2025-07-15 11:41:15'),(2,'Concorde','2025-07-15 11:41:15','2025-07-15 11:41:15'),(3,'Ascerbci','2025-07-15 11:41:15','2025-07-15 11:41:15'),(4,'Elkan','2025-07-15 11:41:15','2025-07-15 11:41:15'),(5,'Otros','2025-07-15 11:41:15','2025-07-15 11:41:15'),(6,'Oracle','2025-07-15 11:41:15','2025-07-15 11:41:15'),(7,'SQL SERVER','2025-07-15 11:41:15','2025-07-15 11:41:15'),(8,'Ascerzen','2025-07-15 11:41:15','2025-07-15 11:41:15'),(9,'C-Concorde','2025-07-15 11:41:15','2025-07-15 11:41:15'),(10,'C-Breton','2025-07-15 11:41:15','2025-07-15 11:41:15');
/*!40000 ALTER TABLE `tipos_servidores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_solicitantes`
--

DROP TABLE IF EXISTS `tipos_solicitantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_solicitantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=601 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_solicitantes`
--

LOCK TABLES `tipos_solicitantes` WRITE;
/*!40000 ALTER TABLE `tipos_solicitantes` DISABLE KEYS */;
INSERT INTO `tipos_solicitantes` VALUES (452,'Pablo Lorca','2025-07-15 11:38:59','2025-07-15 11:38:59'),(453,'Ricardo Fernandez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(454,'LUIS ADASME','2025-07-15 11:38:59','2025-07-15 11:38:59'),(455,'Alice Esparza','2025-07-15 11:38:59','2025-07-15 11:38:59'),(456,'Adrea Perez  Alarcon','2025-07-15 11:38:59','2025-07-15 11:38:59'),(457,'Johanna Gana','2025-07-15 11:38:59','2025-07-15 11:38:59'),(458,'Carolina Morales','2025-07-15 11:38:59','2025-07-15 11:38:59'),(459,'Felipe Diaz','2025-07-15 11:38:59','2025-07-15 11:38:59'),(460,'Brayan Parra','2025-07-15 11:38:59','2025-07-15 11:38:59'),(461,'Francisco Aguilar','2025-07-15 11:38:59','2025-07-15 11:38:59'),(462,'Hernan Ferrada','2025-07-15 11:38:59','2025-07-15 11:38:59'),(463,'Silvana Flores','2025-07-15 11:38:59','2025-07-15 11:38:59'),(464,'Ricardo Paz Labra','2025-07-15 11:38:59','2025-07-15 11:38:59'),(465,'Gustavo Elgueta','2025-07-15 11:38:59','2025-07-15 11:38:59'),(466,'Nancy Godoy','2025-07-15 11:38:59','2025-07-15 11:38:59'),(467,'Luisana Hurtado','2025-07-15 11:38:59','2025-07-15 11:38:59'),(468,'Giovanni Ricciardi','2025-07-15 11:38:59','2025-07-15 11:38:59'),(469,'Orlando Villalobos','2025-07-15 11:38:59','2025-07-15 11:38:59'),(470,'Paola Diaz','2025-07-15 11:38:59','2025-07-15 11:38:59'),(471,'Ricardo Salgado','2025-07-15 11:38:59','2025-07-15 11:38:59'),(472,'Maria Ayala','2025-07-15 11:38:59','2025-07-15 11:38:59'),(473,'Cindy Berroteran','2025-07-15 11:38:59','2025-07-15 11:38:59'),(474,'Carlos Sepulveda','2025-07-15 11:38:59','2025-07-15 11:38:59'),(475,'Juan Lizama','2025-07-15 11:38:59','2025-07-15 11:38:59'),(476,'Juan Rivera','2025-07-15 11:38:59','2025-07-15 11:38:59'),(477,'Juan Pinto','2025-07-15 11:38:59','2025-07-15 11:38:59'),(478,'Jerson Pacheco','2025-07-15 11:38:59','2025-07-15 11:38:59'),(479,'Jorge Meza','2025-07-15 11:38:59','2025-07-15 11:38:59'),(480,'Andersson Castañeda','2025-07-15 11:38:59','2025-07-15 11:38:59'),(481,'Jonathan Yanez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(482,'Alain Diaz','2025-07-15 11:38:59','2025-07-15 11:38:59'),(483,'Catalina berrios','2025-07-15 11:38:59','2025-07-15 11:38:59'),(484,'Cristian Puga','2025-07-15 11:38:59','2025-07-15 11:38:59'),(485,'Jose Muñoz','2025-07-15 11:38:59','2025-07-15 11:38:59'),(486,'Richard Montecinos','2025-07-15 11:38:59','2025-07-15 11:38:59'),(487,'Sandra Cataldo','2025-07-15 11:38:59','2025-07-15 11:38:59'),(488,'Esteban Ortega','2025-07-15 11:38:59','2025-07-15 11:38:59'),(489,'Caricia Jara','2025-07-15 11:38:59','2025-07-15 11:38:59'),(490,'ALEJANDRO CACERES','2025-07-15 11:38:59','2025-07-15 11:38:59'),(491,'CAMILA CAMPOS','2025-07-15 11:38:59','2025-07-15 11:38:59'),(492,'ELENA ALTAMIRANO','2025-07-15 11:38:59','2025-07-15 11:38:59'),(493,'Macarena Pino','2025-07-15 11:38:59','2025-07-15 11:38:59'),(494,'robert wilhelm','2025-07-15 11:38:59','2025-07-15 11:38:59'),(495,'Felipe Peña','2025-07-15 11:38:59','2025-07-15 11:38:59'),(496,'Francisco Rojas','2025-07-15 11:38:59','2025-07-15 11:38:59'),(497,'Carlos Criollo','2025-07-15 11:38:59','2025-07-15 11:38:59'),(498,'Eduardo yuretic','2025-07-15 11:38:59','2025-07-15 11:38:59'),(499,'Esteban Inostroza','2025-07-15 11:38:59','2025-07-15 11:38:59'),(500,'Sebastian Vargas','2025-07-15 11:38:59','2025-07-15 11:38:59'),(501,'fernanda garcia','2025-07-15 11:38:59','2025-07-15 11:38:59'),(502,'YERKO LUENGO','2025-07-15 11:38:59','2025-07-15 11:38:59'),(503,'Andrea Riquelme','2025-07-15 11:38:59','2025-07-15 11:38:59'),(504,'Omar Ramirez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(505,'Deyza Bellorin','2025-07-15 11:38:59','2025-07-15 11:38:59'),(506,'Sebastian Reales','2025-07-15 11:38:59','2025-07-15 11:38:59'),(507,'Armando Avello','2025-07-15 11:38:59','2025-07-15 11:38:59'),(508,'Hugo Huichalao ','2025-07-15 11:38:59','2025-07-15 11:38:59'),(509,'Juan Carlos Gomez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(510,'Oscar Burbano','2025-07-15 11:38:59','2025-07-15 11:38:59'),(511,'Luis Caceres','2025-07-15 11:38:59','2025-07-15 11:38:59'),(512,'Mildren Rojas','2025-07-15 11:38:59','2025-07-15 11:38:59'),(513,'Juan Carlos Fuentes','2025-07-15 11:38:59','2025-07-15 11:38:59'),(514,'Juan Gomez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(515,'Hector Mancilla','2025-07-15 11:38:59','2025-07-15 11:38:59'),(516,'Javier Serrano','2025-07-15 11:38:59','2025-07-15 11:38:59'),(517,'Jorge Rantul','2025-07-15 11:38:59','2025-07-15 11:38:59'),(518,'Daniel Reyes','2025-07-15 11:38:59','2025-07-15 11:38:59'),(519,'Gustavo Arana','2025-07-15 11:38:59','2025-07-15 11:38:59'),(520,'Francisco Raffo','2025-07-15 11:38:59','2025-07-15 11:38:59'),(521,'Natalia Reyes','2025-07-15 11:38:59','2025-07-15 11:38:59'),(522,'Luis Muñoz','2025-07-15 11:38:59','2025-07-15 11:38:59'),(523,'Sebastian  Cubillos','2025-07-15 11:38:59','2025-07-15 11:38:59'),(524,'Andrea Puelma','2025-07-15 11:38:59','2025-07-15 11:38:59'),(525,'PULMA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(526,'Jorge Garcia','2025-07-15 11:38:59','2025-07-15 11:38:59'),(527,'Victor Lopez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(528,'boris  fuentes','2025-07-15 11:38:59','2025-07-15 11:38:59'),(529,'Silvana Landaeta','2025-07-15 11:38:59','2025-07-15 11:38:59'),(530,'Judith Cid','2025-07-15 11:38:59','2025-07-15 11:38:59'),(531,'Victor Carreño','2025-07-15 11:38:59','2025-07-15 11:38:59'),(532,'Carlos Hernandez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(533,'CECILIA ZAVALA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(534,'Renzo Baeza','2025-07-15 11:38:59','2025-07-15 11:38:59'),(535,'Juan Fuentes','2025-07-15 11:38:59','2025-07-15 11:38:59'),(536,'Felipe Ramirez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(537,'mARITZA ARANCIBIA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(538,'Camila Frex','2025-07-15 11:38:59','2025-07-15 11:38:59'),(539,'paula leupin','2025-07-15 11:38:59','2025-07-15 11:38:59'),(540,'Rebeca Gonzalez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(541,'Gonzalo Donoso','2025-07-15 11:38:59','2025-07-15 11:38:59'),(542,'victoria Miranda','2025-07-15 11:38:59','2025-07-15 11:38:59'),(543,'FranciscoRaffo','2025-07-15 11:38:59','2025-07-15 11:38:59'),(544,'Fabian Castañeda','2025-07-15 11:38:59','2025-07-15 11:38:59'),(545,'Sergio Galaz','2025-07-15 11:38:59','2025-07-15 11:38:59'),(546,'Roberto Gonzalez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(547,'Luis Arias','2025-07-15 11:38:59','2025-07-15 11:38:59'),(548,'Claudio Romo','2025-07-15 11:38:59','2025-07-15 11:38:59'),(549,'Fernando Jil','2025-07-15 11:38:59','2025-07-15 11:38:59'),(550,'Pamela Pulgar','2025-07-15 11:38:59','2025-07-15 11:38:59'),(551,'Anderson Castañeda','2025-07-15 11:38:59','2025-07-15 11:38:59'),(552,'Francisco Rojas ','2025-07-15 11:38:59','2025-07-15 11:38:59'),(553,'Anthony Moronta','2025-07-15 11:38:59','2025-07-15 11:38:59'),(554,'Maria sSanchez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(555,'Luis Dellan','2025-07-15 11:38:59','2025-07-15 11:38:59'),(556,'Dominique Vergara','2025-07-15 11:38:59','2025-07-15 11:38:59'),(557,'Soledad Obregon','2025-07-15 11:38:59','2025-07-15 11:38:59'),(558,'Mariano Castillo','2025-07-15 11:38:59','2025-07-15 11:38:59'),(559,'jose gom','2025-07-15 11:38:59','2025-07-15 11:38:59'),(560,'INGRID HEVIA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(561,'Erika Logane','2025-07-15 11:38:59','2025-07-15 11:38:59'),(562,'Sandra Williamson','2025-07-15 11:38:59','2025-07-15 11:38:59'),(563,'Jose Luis Castro','2025-07-15 11:38:59','2025-07-15 11:38:59'),(564,'Andreson Castañeda','2025-07-15 11:38:59','2025-07-15 11:38:59'),(565,'Rodrigo Vistoso','2025-07-15 11:38:59','2025-07-15 11:38:59'),(566,'Korenia Avendaño','2025-07-15 11:38:59','2025-07-15 11:38:59'),(567,'Alison Hidalgo','2025-07-15 11:38:59','2025-07-15 11:38:59'),(568,'Adrian Uribe','2025-07-15 11:38:59','2025-07-15 11:38:59'),(569,'Camila Tapia','2025-07-15 11:38:59','2025-07-15 11:38:59'),(570,'Valeria Herrera','2025-07-15 11:38:59','2025-07-15 11:38:59'),(571,'Claudio Arce','2025-07-15 11:38:59','2025-07-15 11:38:59'),(572,'CATHERINE kULLMER','2025-07-15 11:38:59','2025-07-15 11:38:59'),(573,'Hertor Mancilla','2025-07-15 11:38:59','2025-07-15 11:38:59'),(574,'FELIPE ORELLANA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(575,'ARYSALEN VERBICKAS','2025-07-15 11:38:59','2025-07-15 11:38:59'),(576,'Yusmarys Guatarama','2025-07-15 11:38:59','2025-07-15 11:38:59'),(577,'Euka','2025-07-15 11:38:59','2025-07-15 11:38:59'),(578,'eukaris','2025-07-15 11:38:59','2025-07-15 11:38:59'),(579,'JOSE URZUA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(580,'felipe gamboa','2025-07-15 11:38:59','2025-07-15 11:38:59'),(581,'ROBERTO VALENCIA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(582,'patricio mez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(583,'Carlos Zuñiga','2025-07-15 11:38:59','2025-07-15 11:38:59'),(584,'Jorge Jara','2025-07-15 11:38:59','2025-07-15 11:38:59'),(585,'Jorge Hormazabal','2025-07-15 11:38:59','2025-07-15 11:38:59'),(586,'Orlando Villalobos ','2025-07-15 11:38:59','2025-07-15 11:38:59'),(587,'Carla Iquira','2025-07-15 11:38:59','2025-07-15 11:38:59'),(588,'esparx','2025-07-15 11:38:59','2025-07-15 11:38:59'),(589,'Luis Caseres','2025-07-15 11:38:59','2025-07-15 11:38:59'),(590,'Ivan Perez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(591,'Fabian Becerra','2025-07-15 11:38:59','2025-07-15 11:38:59'),(592,'Gustavo Ubilla','2025-07-15 11:38:59','2025-07-15 11:38:59'),(593,'alejandra torres','2025-07-15 11:38:59','2025-07-15 11:38:59'),(594,'Carlos Torres','2025-07-15 11:38:59','2025-07-15 11:38:59'),(595,'SUSANA CA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(596,'cinthya ramis','2025-07-15 11:38:59','2025-07-15 11:38:59'),(597,'MARIA RIVERA','2025-07-15 11:38:59','2025-07-15 11:38:59'),(598,'Carlols Torres','2025-07-15 11:38:59','2025-07-15 11:38:59'),(599,'karina lopez','2025-07-15 11:38:59','2025-07-15 11:38:59'),(600,'prisci','2025-07-15 11:38:59','2025-07-15 11:38:59');
/*!40000 ALTER TABLE `tipos_solicitantes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipos_solicitudes`
--

DROP TABLE IF EXISTS `tipos_solicitudes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipos_solicitudes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipos_solicitudes`
--

LOCK TABLES `tipos_solicitudes` WRITE;
/*!40000 ALTER TABLE `tipos_solicitudes` DISABLE KEYS */;
INSERT INTO `tipos_solicitudes` VALUES (1,'Proactivanet','2025-07-15 11:42:07','2025-07-15 11:42:07'),(2,'No Proactivanet','2025-07-15 11:42:07','2025-07-15 11:42:07');
/*!40000 ALTER TABLE `tipos_solicitudes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `area` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Operador',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Ignacio Riquelme','ignacio.riquelme@cliptecnologia.com','analista',NULL,'$2y$12$kkaz1pDzGfl3TkXflWv01urXOrLUbHgFF/IC6tYKL4WUsq0p20Koe',NULL,NULL,NULL,'OQXY9x66p4s1l7AmbWQdgcw5H18zZrXA69kqNUFTNrwMUlvaDDeaZjDTgLJq','2025-07-15 11:45:05','2025-07-15 11:45:05');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-07-15 19:19:40
