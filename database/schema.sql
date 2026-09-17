-- ====================================================================
-- SISTEMA DE MESA DE PARTES VIRTUAL — IESP TÚPAC AMARU CUSCO
-- DDL - Estructura de Base de Datos
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `mesa_partes_tupac` 
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `mesa_partes_tupac`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. UNIDADES INSTITUCIONALES
DROP TABLE IF EXISTS `unidades`;
CREATE TABLE `unidades` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `descripcion` TEXT NULL,
    `responsable` VARCHAR(150) NULL,
    `cargo` VARCHAR(100) NULL,
    `correo` VARCHAR(100) NULL,
    `telefono` VARCHAR(50) NULL,
    `estado` TINYINT(1) NOT NULL DEFAULT 1,
    `orden` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_unidades_estado` (`estado`),
    INDEX `idx_unidades_orden` (`orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. PROGRAMAS DE ESTUDIOS
DROP TABLE IF EXISTS `programas_estudio`;
CREATE TABLE `programas_estudio` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `estado` TINYINT(1) NOT NULL DEFAULT 1,
    `orden` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_programas_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. CATEGORÍAS DE TRÁMITES FUT
DROP TABLE IF EXISTS `categorias_tramite`;
CREATE TABLE `categorias_tramite` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(150) NOT NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TIPOS DE TRÁMITES
DROP TABLE IF EXISTS `tipos_tramite`;
CREATE TABLE `tipos_tramite` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `categoria_id` INT NOT NULL,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(200) NOT NULL,
    `descripcion` TEXT NULL,
    `unidad_sugerida_id` INT NULL,
    `requisitos` TEXT NULL,
    `plazo_dias` INT NOT NULL DEFAULT 5,
    `costo` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `requiere_pago` TINYINT(1) NOT NULL DEFAULT 0,
    `admite_virtual` TINYINT(1) NOT NULL DEFAULT 1,
    `estado` TINYINT(1) NOT NULL DEFAULT 1,
    `orden` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_tipostramite_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categorias_tramite`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tipostramite_unidad` FOREIGN KEY (`unidad_sugerida_id`) REFERENCES `unidades`(`id`) ON DELETE SET NULL,
    INDEX `idx_tipos_tramite_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. ESTADOS DEL EXPEDIENTE
DROP TABLE IF EXISTS `estados_expediente`;
CREATE TABLE `estados_expediente` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(100) NOT NULL,
    `color` VARCHAR(20) NOT NULL DEFAULT '#6B7280',
    `icono` VARCHAR(50) NOT NULL DEFAULT 'bi-clock',
    `orden` INT NOT NULL DEFAULT 0,
    `es_publico` TINYINT(1) NOT NULL DEFAULT 1,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. PRIORIDADES
DROP TABLE IF EXISTS `prioridades`;
CREATE TABLE `prioridades` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `codigo` VARCHAR(50) NOT NULL UNIQUE,
    `nombre` VARCHAR(50) NOT NULL,
    `color` VARCHAR(20) NOT NULL DEFAULT '#6B7280',
    `orden` INT NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. ROLES DEL SISTEMA
DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) NULL,
    `estado` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. PERMISOS RBAC
DROP TABLE IF EXISTS `permisos`;
CREATE TABLE `permisos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `modulo` VARCHAR(100) NOT NULL,
    `nombre` VARCHAR(150) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `descripcion` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 9. ROLES Y PERMISOS (INTERMEDIA)
DROP TABLE IF EXISTS `roles_permisos`;
CREATE TABLE `roles_permisos` (
    `rol_id` INT NOT NULL,
    `permiso_id` INT NOT NULL,
    PRIMARY KEY (`rol_id`, `permiso_id`),
    CONSTRAINT `fk_rp_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permiso` FOREIGN KEY (`permiso_id`) REFERENCES `permisos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 10. USUARIOS
DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `dni` VARCHAR(20) NOT NULL UNIQUE,
    `nombres` VARCHAR(100) NOT NULL,
    `apellidos` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password_hash` VARCHAR(255) NOT NULL,
    `telefono` VARCHAR(50) NULL,
    `cargo` VARCHAR(100) NULL,
    `unidad_id` INT NULL,
    `estado` TINYINT(1) NOT NULL DEFAULT 1,
    `debe_cambiar_password` TINYINT(1) NOT NULL DEFAULT 1,
    `ultimo_acceso` DATETIME NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_usuarios_unidad` FOREIGN KEY (`unidad_id`) REFERENCES `unidades`(`id`) ON DELETE SET NULL,
    INDEX `idx_usuarios_username` (`username`),
    INDEX `idx_usuarios_email` (`email`),
    INDEX `idx_usuarios_dni` (`dni`),
    INDEX `idx_usuarios_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 11. USUARIOS Y ROLES (INTERMEDIA)
DROP TABLE IF EXISTS `usuarios_roles`;
CREATE TABLE `usuarios_roles` (
    `usuario_id` INT NOT NULL,
    `rol_id` INT NOT NULL,
    PRIMARY KEY (`usuario_id`, `rol_id`),
    CONSTRAINT `fk_ur_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_ur_rol` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 12. EXPEDIENTES
DROP TABLE IF EXISTS `expedientes`;
CREATE TABLE `expedientes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `numero_expediente` VARCHAR(50) NOT NULL UNIQUE,
    `codigo_seguimiento` VARCHAR(30) NOT NULL UNIQUE,
    `solicito` VARCHAR(255) NOT NULL,
    `sumilla` TEXT NOT NULL,
    `apellido_paterno` VARCHAR(100) NOT NULL,
    `apellido_materno` VARCHAR(100) NOT NULL,
    `nombres` VARCHAR(100) NOT NULL,
    `dni` VARCHAR(20) NOT NULL,
    `correo` VARCHAR(100) NOT NULL,
    `direccion_domiciliaria` VARCHAR(255) NOT NULL,
    `celular` VARCHAR(30) NOT NULL,
    `es_estudiante_egresado` TINYINT(1) NOT NULL DEFAULT 0,
    `programa_id` INT NULL,
    `codigo_estudiante` VARCHAR(50) NULL,
    `anio_ingreso` VARCHAR(10) NULL,
    `anio_egreso` VARCHAR(10) NULL,
    `tipo_tramite_id` INT NOT NULL,
    `fundamento_peticion` LONGTEXT NOT NULL,
    `estado_id` INT NOT NULL,
    `prioridad_id` INT NOT NULL,
    `unidad_actual_id` INT NOT NULL,
    `unidad_responsable_id` INT NULL,
    `usuario_responsable_id` INT NULL,
    `fecha_ingreso` DATETIME NOT NULL,
    `fecha_finalizacion` DATETIME NULL,
    `observacion_publica` TEXT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_expediente_programa` FOREIGN KEY (`programa_id`) REFERENCES `programas_estudio`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_expediente_tipotramite` FOREIGN KEY (`tipo_tramite_id`) REFERENCES `tipos_tramite`(`id`),
    CONSTRAINT `fk_expediente_estado` FOREIGN KEY (`estado_id`) REFERENCES `estados_expediente`(`id`),
    CONSTRAINT `fk_expediente_prioridad` FOREIGN KEY (`prioridad_id`) REFERENCES `prioridades`(`id`),
    CONSTRAINT `fk_expediente_unidadactual` FOREIGN KEY (`unidad_actual_id`) REFERENCES `unidades`(`id`),
    CONSTRAINT `fk_expediente_unidadresp` FOREIGN KEY (`unidad_responsable_id`) REFERENCES `unidades`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_expediente_usuarioresp` FOREIGN KEY (`usuario_responsable_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_exp_numero` (`numero_expediente`),
    INDEX `idx_exp_codigo` (`codigo_seguimiento`),
    INDEX `idx_exp_dni` (`dni`),
    INDEX `idx_exp_estado` (`estado_id`),
    INDEX `idx_exp_unidadactual` (`unidad_actual_id`),
    INDEX `idx_exp_fecha_ingreso` (`fecha_ingreso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 13. EXPEDIENTE DOCUMENTOS
DROP TABLE IF EXISTS `expediente_documentos`;
CREATE TABLE `expediente_documentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `expediente_id` INT NOT NULL,
    `usuario_id` INT NULL,
    `nombre_original` VARCHAR(255) NOT NULL,
    `nombre_archivo` VARCHAR(255) NOT NULL,
    `ruta_archivo` VARCHAR(255) NOT NULL,
    `mime_type` VARCHAR(100) NOT NULL,
    `tamanio_bytes` BIGINT NOT NULL,
    `hash_sha256` VARCHAR(64) NOT NULL,
    `tipo_documento` ENUM('ADJUNTO_INICIAL', 'INFORME_RESPUESTA', 'PROVEIDO', 'ANEXO') NOT NULL DEFAULT 'ADJUNTO_INICIAL',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_expdocs_expediente` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_expdocs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_expdocs_expediente` (`expediente_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 14. EXPEDIENTE MOVIMIENTOS (TRAZABILIDAD INMUTABLE)
DROP TABLE IF EXISTS `expediente_movimientos`;
CREATE TABLE `expediente_movimientos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `expediente_id` INT NOT NULL,
    `tipo_movimiento` VARCHAR(50) NOT NULL,
    `unidad_origen_id` INT NULL,
    `unidad_destino_id` INT NULL,
    `usuario_id` INT NULL,
    `estado_anterior_id` INT NULL,
    `estado_nuevo_id` INT NOT NULL,
    `observacion` TEXT NULL,
    `es_publico` TINYINT(1) NOT NULL DEFAULT 1,
    `ip` VARCHAR(45) NOT NULL DEFAULT '127.0.0.1',
    `user_agent` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_expmov_expediente` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_expmov_origen` FOREIGN KEY (`unidad_origen_id`) REFERENCES `unidades`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_expmov_destino` FOREIGN KEY (`unidad_destino_id`) REFERENCES `unidades`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_expmov_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_expmov_estadoant` FOREIGN KEY (`estado_anterior_id`) REFERENCES `estados_expediente`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_expmov_estadonuevo` FOREIGN KEY (`estado_nuevo_id`) REFERENCES `estados_expediente`(`id`),
    INDEX `idx_expmov_expediente` (`expediente_id`),
    INDEX `idx_expmov_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 15. NOTIFICACIONES
DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `unidad_id` INT NULL,
    `titulo` VARCHAR(150) NOT NULL,
    `mensaje` TEXT NOT NULL,
    `url` VARCHAR(255) NULL,
    `leido` TINYINT(1) NOT NULL DEFAULT 0,
    `tipo` VARCHAR(50) NOT NULL DEFAULT 'info',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notif_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_notif_unidad` FOREIGN KEY (`unidad_id`) REFERENCES `unidades`(`id`) ON DELETE CASCADE,
    INDEX `idx_notif_usuario` (`usuario_id`, `leido`),
    INDEX `idx_notif_unidad` (`unidad_id`, `leido`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 16. CONFIGURACIONES GENERALES Y DE APARIENCIA
DROP TABLE IF EXISTS `configuraciones`;
CREATE TABLE `configuraciones` (
    `clave` VARCHAR(100) NOT NULL PRIMARY KEY,
    `valor` TEXT NULL,
    `descripcion` VARCHAR(255) NULL,
    `grupo` VARCHAR(50) NOT NULL DEFAULT 'general',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 17. CONFIGURACIÓN SMTP
DROP TABLE IF EXISTS `configuracion_smtp`;
CREATE TABLE `configuracion_smtp` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `host` VARCHAR(150) NOT NULL DEFAULT 'smtp.gmail.com',
    `puerto` INT NOT NULL DEFAULT 587,
    `usuario` VARCHAR(150) NULL,
    `password_encriptado` VARCHAR(255) NULL,
    `seguridad` ENUM('ninguna', 'tls', 'ssl') NOT NULL DEFAULT 'tls',
    `remitente_email` VARCHAR(150) NOT NULL DEFAULT 'mesadepartes@tupacamaru.edu.pe',
    `remitente_nombre` VARCHAR(150) NOT NULL DEFAULT 'Mesa de Partes - IESP Túpac Amaru',
    `activo` TINYINT(1) NOT NULL DEFAULT 0,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 18. AUDITORÍA
DROP TABLE IF EXISTS `auditoria`;
CREATE TABLE `auditoria` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario_id` INT NULL,
    `accion` VARCHAR(100) NOT NULL,
    `modulo` VARCHAR(100) NOT NULL,
    `registro_id` INT NULL,
    `ip` VARCHAR(45) NOT NULL DEFAULT '127.0.0.1',
    `user_agent` VARCHAR(255) NULL,
    `datos_anteriores` JSON NULL,
    `datos_nuevos` JSON NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_auditoria_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
    INDEX `idx_auditoria_usuario` (`usuario_id`),
    INDEX `idx_auditoria_modulo` (`modulo`),
    INDEX `idx_auditoria_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 19. INTENTOS DE LOGIN (RATE LIMITING)
DROP TABLE IF EXISTS `intentos_login`;
CREATE TABLE `intentos_login` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `ip` VARCHAR(45) NOT NULL,
    `username` VARCHAR(100) NOT NULL,
    `fecha_hora` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `exito` TINYINT(1) NOT NULL DEFAULT 0,
    INDEX `idx_intentos_ip` (`ip`, `fecha_hora`),
    INDEX `idx_intentos_username` (`username`, `fecha_hora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
