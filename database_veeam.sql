-- =====================================================
-- QUERIES PARA CREAR TABLAS DE REPORTE VEEAM
-- Base de Datos: Laravel (o la que estés usando)
-- Fecha: 13/12/2025
-- =====================================================

-- Tabla principal: reportes_veeam
CREATE TABLE IF NOT EXISTS `reportes_veeam` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `numero_ticket` varchar(50) DEFAULT 'N/A',
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('Warning','Failed') NOT NULL,
  `job_fallido` varchar(500) NOT NULL,
  `seguimiento` text DEFAULT NULL,
  `descripcion_error` text NOT NULL,
  `estado_ticket` enum('Informativo','Pendiente','Resuelto') NOT NULL,
  `creado_por` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_fecha_inicio` (`fecha_inicio`),
  KEY `idx_estado_ticket` (`estado_ticket`),
  KEY `idx_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- COMENTARIOS SOBRE LOS CAMPOS
-- =====================================================
-- numero_ticket: Número de ticket (opcional, default 'N/A', alfanumérico)
-- fecha_inicio: Fecha de inicio del reporte (formato: YYYY-MM-DD, obligatorio)
-- fecha_fin: Fecha de finalización (se completa automáticamente según estado_ticket)
-- estado: Estado del job (Warning o Failed, obligatorio)
-- job_fallido: Nombre del job que falló (máx 500 caracteres, obligatorio)
-- seguimiento: Historial de seguimientos con formato "dd/mm/aaaa - Usuario: texto"
-- descripcion_error: Detalle de los errores (máx 3000 caracteres en aplicación, TEXT en BD)
-- estado_ticket: Estado del ticket (Informativo, Pendiente, Resuelto, obligatorio)
-- creado_por: Nombre del usuario que creó el reporte
-- created_at/updated_at: Timestamps automáticos de Laravel

-- =====================================================
-- INDICES ADICIONALES (OPCIONAL - Para mejor rendimiento)
-- =====================================================
-- Ya incluidos arriba:
-- - idx_fecha_inicio: Para búsquedas por fecha
-- - idx_estado_ticket: Para filtrar por estado del ticket
-- - idx_estado: Para filtrar por Warning/Failed

-- =====================================================
-- EJEMPLO DE INSERT
-- =====================================================
-- INSERT INTO `reportes_veeam` 
-- (`numero_ticket`, `fecha_inicio`, `fecha_fin`, `estado`, `job_fallido`, 
--  `seguimiento`, `descripcion_error`, `estado_ticket`, `creado_por`, 
--  `created_at`, `updated_at`) 
-- VALUES 
-- ('TICKET-12345', '2025-12-13', NULL, 'Failed', 'Backup_Production_Daily',
--  '13/12/2025 - Juan Pérez: Reportado error inicial', 
--  'Error al conectar con el servidor de destino. Timeout después de 30 segundos.',
--  'Pendiente', 'Juan Pérez', NOW(), NOW());

-- =====================================================
-- VERIFICAR TABLA CREADA
-- =====================================================
-- SHOW CREATE TABLE reportes_veeam;
-- DESCRIBE reportes_veeam;

-- =====================================================
-- FIN DEL SCRIPT
-- =====================================================
