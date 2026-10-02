-- =====================================================================
-- MESA DE PARTES VIRTUAL - IESP TÚPAC AMARU
-- Actualización: Numeración configurable de expedientes
--
-- Este archivo es para instalaciones EXISTENTES que ya tienen la base
-- de datos creada con una versión anterior de database/schema.sql.
-- Las instalaciones nuevas NO lo necesitan: database/schema.sql ya
-- incluye la tabla `correlativos`.
--
-- Uso:  mysql -u root mesa_partes_tupac < database/upgrade_numeracion.sql
-- =====================================================================

-- 1. Tabla de correlativos (secuencia atómica por año y formato)
CREATE TABLE IF NOT EXISTS `correlativos` (
    `clave` VARCHAR(60) NOT NULL PRIMARY KEY,
    `ultimo_numero` INT UNSIGNED NOT NULL DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Parámetros de numeración (INSERT idempotente: no pisa lo ya configurado)
INSERT INTO `configuraciones` (`clave`, `valor`, `descripcion`, `grupo`) VALUES
('expediente_num_sigla', 'EXP', 'Sigla o prefijo del número de expediente', 'numeracion'),
('expediente_num_incluir_anio', '1', 'Incluir el año en el formato del número de expediente', 'numeracion'),
('expediente_num_digitos', '6', 'Cantidad de dígitos del correlativo de expedientes', 'numeracion'),
('expediente_num_inicio', '1', 'Número inicial del correlativo al iniciar un nuevo período', 'numeracion')
ON DUPLICATE KEY UPDATE `descripcion` = VALUES(`descripcion`), `grupo` = VALUES(`grupo`);

-- 3. Sembrar el correlativo del año en curso a partir del último expediente
--    ya registrado, de modo que la numeración continúe sin colisiones.
INSERT INTO `correlativos` (`clave`, `ultimo_numero`)
SELECT CONCAT('EXPEDIENTE-', YEAR(CURDATE())),
       COALESCE(MAX(CAST(RIGHT(`numero_expediente`, 6) AS UNSIGNED)), 0)
FROM `expedientes`
WHERE `numero_expediente` LIKE CONCAT('EXP-', YEAR(CURDATE()), '-%')
ON DUPLICATE KEY UPDATE `ultimo_numero` = GREATEST(`ultimo_numero`, VALUES(`ultimo_numero`));

-- Verificación
SELECT `clave`, `ultimo_numero` FROM `correlativos`;
SELECT `clave`, `valor` FROM `configuraciones` WHERE `grupo` = 'numeracion';