-- ====================================================================
-- SISTEMA DE MESA DE PARTES VIRTUAL — IESP TÚPAC AMARU CUSCO
-- DML - Datos Iniciales (Seeders)
-- ====================================================================

USE `mesa_partes_tupac`;

SET FOREIGN_KEY_CHECKS = 0;

-- 1. UNIDADES INSTITUCIONALES (Sección 4)
TRUNCATE TABLE `unidades`;
INSERT INTO `unidades` (`id`, `codigo`, `nombre`, `descripcion`, `responsable`, `cargo`, `correo`, `telefono`, `estado`, `orden`) VALUES
(1, 'DG', 'Dirección General', 'Máxima autoridad institucional y directiva', 'Dr. Director General', 'Director General', 'direccion@tupacamaru.edu.pe', '084-223344', 1, 1),
(2, 'UA', 'Unidad Académica', 'Gestión, supervisión y desarrollo pedagógico', 'Mg. Jefe Académico', 'Jefe de Unidad Académica', 'academica@tupacamaru.edu.pe', '084-223345', 1, 2),
(3, 'SA', 'Secretaría Académica', 'Control escolar, actas, certificados y títulos', 'Lic. Secretaria Académica', 'Secretaria Académica', 'secretaria.academica@tupacamaru.edu.pe', '084-223346', 1, 3),
(4, 'UB', 'Unidad de Bienestar', 'Bienestar del estudiante, salud y psicopedagogía', 'Lic. Bienestar Estudiantil', 'Jefe de Bienestar', 'bienestar@tupacamaru.edu.pe', '084-223347', 1, 4),
(5, 'UC', 'Unidad de Calidad', 'Acreditación, aseguramiento y control de la calidad', 'Ing. Coordinador de Calidad', 'Jefe de Calidad', 'calidad@tupacamaru.edu.pe', '084-223348', 1, 5),
(6, 'OP', 'Oficina de Personal', 'Gestión del talento humano, legajos y control de asistencia', 'Abog. Jefe de Personal', 'Jefe de Personal', 'personal@tupacamaru.edu.pe', '084-223349', 1, 6),
(7, 'OA', 'Oficina de Administración', 'Gestión económica, financiera y presupuestal', 'C.P.C. Administrador', 'Jefe de Administración', 'administracion@tupacamaru.edu.pe', '084-223350', 1, 7),
(8, 'BIB', 'Biblioteca', 'Servicios bibliográficos y recursos de investigación', 'Lic. Bibliotecario Central', 'Responsable de Biblioteca', 'biblioteca@tupacamaru.edu.pe', '084-223351', 1, 8),
(9, 'OAB', 'Oficina de Abastecimiento', 'Contrataciones, compras y adquisiciones institucionales', 'Lic. Abastecimiento', 'Jefe de Abastecimiento', 'abastecimiento@tupacamaru.edu.pe', '084-223352', 1, 9),
(10, 'OPAT', 'Oficina de Patrimonio', 'Control de bienes patrimoniales e inventario', 'Ing. Patrimonio', 'Responsable de Patrimonio', 'patrimonio@tupacamaru.edu.pe', '084-223353', 1, 10);

-- 2. PROGRAMAS DE ESTUDIOS (Sección 16)
TRUNCATE TABLE `programas_estudio`;
INSERT INTO `programas_estudio` (`id`, `codigo`, `nombre`, `estado`, `orden`) VALUES
(1, 'DSI', 'Desarrollo de Sistemas de Información', 1, 1),
(2, 'CON', 'Contabilidad', 1, 2),
(3, 'GOT', 'Guía Oficial de Turismo', 1, 3),
(4, 'EAU', 'Electricidad y Electrónica Industrial', 1, 4),
(5, 'MAU', 'Mecánica Automotriz', 1, 5),
(6, 'MPR', 'Mecánica de Producción', 1, 6),
(7, 'ETE', 'Enfermería Técnica', 1, 7),
(8, 'LAB', 'Laboratorio Clínico y Anatomía Patológica', 1, 8),
(9, 'PRO', 'Prótesis Dental', 1, 9);

-- 3. CATEGORÍAS DE TRÁMITE DEL FUT (Sección 6)
TRUNCATE TABLE `categorias_tramite`;
INSERT INTO `categorias_tramite` (`id`, `codigo`, `nombre`, `orden`) VALUES
(1, 'CAT_01', 'Trámites de Titulación', 1),
(2, 'CAT_02', 'Certificado de', 2),
(3, 'CAT_03', 'Constancia de', 3),
(4, 'CAT_04', 'ESRT (Práctica)', 4),
(5, 'CAT_05', 'Carnet de Estudiante', 5),
(6, 'CAT_06', 'Ficha de Seguimiento', 6),
(7, 'CAT_07', 'Licencia / Reserva de Matrícula', 7),
(8, 'CAT_08', 'Reincorporación', 8),
(9, 'CAT_09', 'Convalidación', 9),
(10, 'CAT_10', 'Examen Extraordinario', 10),
(11, 'CAT_11', 'Rectificación de Datos', 11),
(12, 'CAT_12', 'Sílabo', 12),
(13, 'CAT_13', 'Traslado Interno / Externo', 13),
(14, 'CAT_14', 'Contrata', 14),
(15, 'CAT_15', 'Justificación de', 15),
(16, 'CAT_16', 'Permiso', 16),
(17, 'CAT_17', 'Alquiler de Ambientes y/o Equipos', 17),
(18, 'CAT_18', 'Otros', 18);

-- 4. TIPOS DE TRÁMITE (Sección 6)
TRUNCATE TABLE `tipos_tramite`;
INSERT INTO `tipos_tramite` (`id`, `categoria_id`, `codigo`, `nombre`, `descripcion`, `unidad_sugerida_id`, `requisitos`, `plazo_dias`, `costo`, `requiere_pago`, `admite_virtual`, `estado`, `orden`) VALUES
-- 1. Titulación
(1, 1, '1.1', 'Designación de Asesor', 'Solicitud de designación de asesor de tesis o trabajo de aplicación profesional', 2, 'FUT, Proyecto de Trabajo de Aplicación en PDF', 5, 0.00, 0, 1, 1, 1),
(2, 1, '1.2', 'Designación de Docente Especialista', 'Designación de docente especialista para revisión de trabajo de aplicación', 2, 'FUT, Constancia de asesoría concluida', 5, 0.00, 0, 1, 1, 2),
(3, 1, '1.3', 'Aprobación de Trabajo de Aplicación', 'Aprobación final del informe de trabajo de aplicación profesional', 2, 'FUT, Informe final con visto bueno de asesor y especialista', 7, 0.00, 0, 1, 1, 3),
(4, 1, '1.4', 'Fecha y Hora de Examen', 'Fijación de fecha y hora para sustentación presencial o virtual', 3, 'FUT, Resolución de expedito', 5, 0.00, 0, 1, 1, 4),
(5, 1, '1.5', 'Autorización Compra Formato de Título', 'Autorización para adquisición de cartón oficial de título', 7, 'FUT, Acta de sustentación aprobada', 3, 50.00, 1, 1, 1, 5),
(6, 1, '1.6', 'Otorgamiento de Bachillerato', 'Emisión y registro institucional del grado de bachiller', 3, 'FUT, Certificado de estudios completos, constancia de no adeudo', 10, 80.00, 1, 1, 1, 6),
(7, 1, '1.7', 'Otorgamiento de Título', 'Emisión del título profesional a nombre de la Nación', 3, 'FUT, Expediente completo de titulación, vouchers de pago', 15, 150.00, 1, 1, 1, 7),
(8, 1, '1.8', 'Duplicado de Título', 'Expedición de duplicado de título por pérdida o deterioro', 3, 'FUT, Denuncia policial, publicación en diario', 20, 200.00, 1, 1, 1, 8),
-- 2. Certificados
(9, 2, '2.1', 'Certificado de Estudios', 'Certificación oficial de calificaciones por ciclo', 3, 'FUT, Voucher de pago, fotos tamaño carnet', 5, 25.00, 1, 1, 1, 1),
(10, 2, '2.2', 'Certificado de Bachillerato (Estudios)', 'Certificado de estudios conducentes a bachillerato', 3, 'FUT, Voucher de pago', 5, 30.00, 1, 1, 1, 2),
(11, 2, '2.3', 'Certificado de Módulo', 'Certificación modular formativa', 2, 'FUT, Actas de notas modulares aprobadas', 5, 20.00, 1, 1, 1, 3),
(12, 2, '2.4', 'Certificado de Idiomas', 'Acreditación de dominio de idioma o lengua nativa', 2, 'FUT, Constancia de notas del centro de idiomas', 5, 20.00, 1, 1, 1, 4),
-- 3. Constancias
(13, 3, '3.1', 'Constancia de Estudios', 'Constancia acreditando matrícula vigente', 3, 'FUT, Ficha de matrícula', 3, 10.00, 1, 1, 1, 1),
(14, 3, '3.2', 'Constancia de Conducta', 'Acreditación de conducta y disciplina institucional', 4, 'FUT, DNI del solicitante', 3, 10.00, 1, 1, 1, 2),
(15, 3, '3.3', 'Constancia de Egresado', 'Acreditación de culminación del plan de estudios', 3, 'FUT, Cuadro de notas completo', 5, 20.00, 1, 1, 1, 3),
(16, 3, '3.4', 'Constancia de No Adeudo', 'Constancia de estar al día con biblioteca, talleres y administración', 7, 'FUT', 3, 10.00, 1, 1, 1, 4),
(17, 3, '3.5', 'Constancia de Título en Trámite', 'Constancia provisional durante trámite de título', 3, 'FUT, Cargo de ingreso de expediente de titulación', 3, 15.00, 1, 1, 1, 5),
-- 4. ESRT
(18, 4, '4.1', 'Oficio de Presentación ESRT', 'Carta u oficio institucional dirigido a empresa o entidad receptora', 2, 'FUT, Datos de la empresa de prácticas', 3, 0.00, 0, 1, 1, 1),
(19, 4, '4.2', 'Calificación de Informe ESRT', 'Evaluación y registro del informe final de prácticas modulares', 2, 'FUT, Informe de prácticas visado por la empresa', 7, 0.00, 0, 1, 1, 2),
-- 5 a 14
(20, 5, '5.0', 'Carnet de Estudiante', 'Duplicado o emisión de carnet oficial de educación superior', 4, 'FUT, Ficha de matrícula, voucher', 7, 15.00, 1, 1, 1, 1),
(21, 6, '6.0', 'Ficha de Seguimiento', 'Ficha y encuesta de seguimiento a egresados', 5, 'FUT, Encuesta completada', 2, 0.00, 0, 1, 1, 1),
(22, 7, '7.0', 'Licencia / Reserva de Matrícula', 'Suspensión temporal o reserva de vacante', 3, 'FUT, Sustento documentado', 5, 20.00, 1, 1, 1, 1),
(23, 8, '8.0', 'Reincorporación', 'Retorno a los estudios regulares tras licencia', 3, 'FUT, Resolución de licencia anterior', 5, 25.00, 1, 1, 1, 1),
(24, 9, '9.0', 'Convalidación', 'Convalidación de asignaturas o unidades didácticas', 2, 'FUT, Sílabos visados y certificados oficiales', 10, 50.00, 1, 1, 1, 1),
(25, 10, '10.0', 'Examen Extraordinario', 'Evaluación de recuperación o subsanación', 2, 'FUT, Voucher de derecho de examen', 3, 20.00, 1, 1, 1, 1),
(26, 11, '11.0', 'Rectificación de Datos', 'Corrección de nombres, apellidos o DNI en actas', 3, 'FUT, Partida de nacimiento o DNI de RENIEC', 5, 15.00, 1, 1, 1, 1),
(27, 12, '12.0', 'Sílabo', 'Copia autenticada de sílabo cursado', 2, 'FUT, Recibo de pago por hoja', 5, 10.00, 1, 1, 1, 1),
(28, 13, '13.0', 'Traslado Interno / Externo', 'Cambio de programa de estudio o traslado interinstitucional', 2, 'FUT, Certificados oficiales de estudio', 10, 60.00, 1, 1, 1, 1),
(29, 14, '14.0', 'Contrata', 'Solicitudes referidas a convocatorias y contratos docentes', 6, 'FUT, Curriculum Vitae documentado', 7, 0.00, 0, 1, 1, 1),
-- 15. Justificaciones
(30, 15, '15.1', 'Justificación de Tardanza', 'Justificación administrativa de retraso al centro de labores', 6, 'FUT, Documento de sustento', 2, 0.00, 0, 1, 1, 1),
(31, 15, '15.2', 'Justificación de Omisión de Picado de Tarjeta', 'Descargo por omisión involuntaria de marcación biométrica', 6, 'FUT, Visto bueno del jefe inmediato', 2, 0.00, 0, 1, 1, 2),
(32, 15, '15.3', 'Justificación de Inasistencia', 'Justificación médica o de fuerza mayor por inasistencia', 6, 'FUT, Certificado médico o sustento', 3, 0.00, 0, 1, 1, 3),
-- 16 a 18
(33, 16, '16.0', 'Permiso', 'Solicitud de permiso con o sin goce de haber', 6, 'FUT, Justificación documental', 3, 0.00, 0, 1, 1, 1),
(34, 17, '17.0', 'Alquiler de Ambientes y/o Equipos', 'Uso temporal de auditorio, losas o laboratorios', 7, 'FUT, Propuesta de fechas y requerimiento', 5, 100.00, 1, 1, 1, 1),
(35, 18, '18.0', 'Otros', 'Otras peticiones que no cuenten con procedimiento específico', 1, 'FUT, Documentación de sustento libre', 7, 0.00, 0, 1, 1, 1);

-- 5. ESTADOS DEL EXPEDIENTE (Sección 15)
TRUNCATE TABLE `estados_expediente`;
INSERT INTO `estados_expediente` (`id`, `codigo`, `nombre`, `color`, `icono`, `orden`, `es_publico`, `activo`) VALUES
(1, 'RECIBIDO', 'Recibido', '#6B7280', 'bi-inbox', 1, 1, 1),
(2, 'REGISTRADO', 'Registrado', '#3B82F6', 'bi-file-earmark-check', 2, 1, 1),
(3, 'ENVIADO_A_DIRECCION', 'Enviado a Dirección', '#6366F1', 'bi-send', 3, 1, 1),
(4, 'EN_REVISION', 'En Revisión de Dirección', '#8B5CF6', 'bi-search', 4, 1, 1),
(5, 'DERIVADO', 'Derivado a Unidad', '#F97316', 'bi-arrow-right-circle', 5, 1, 1),
(6, 'RECEPCIONADO', 'Recepcionado por Unidad', '#0D9488', 'bi-check2-circle', 6, 1, 1),
(7, 'EN_TRAMITE', 'En Trámite', '#EAB308', 'bi-gear-wide-connected', 7, 1, 1),
(8, 'PENDIENTE_INFORMACION', 'Pendiente de Información', '#F59E0B', 'bi-hourglass-split', 8, 1, 1),
(9, 'OBSERVADO', 'Observado', '#B3261E', 'bi-exclamation-triangle', 9, 1, 1),
(10, 'DEVUELTO', 'Devuelto', '#DC2626', 'bi-arrow-return-left', 10, 1, 1),
(11, 'RESPONDIDO', 'Respondido por Unidad', '#10B981', 'bi-reply-all', 11, 1, 1),
(12, 'EN_REVISION_DIRECCION', 'Revisión Final Dirección', '#8B5CF6', 'bi-clipboard-check', 12, 1, 1),
(13, 'APROBADO', 'Aprobado', '#059669', 'bi-patch-check', 13, 1, 1),
(14, 'FINALIZADO', 'Finalizado', '#16A34A', 'bi-check-circle-fill', 14, 1, 1),
(15, 'ARCHIVADO', 'Archivado', '#4B5563', 'bi-archive', 15, 0, 1),
(16, 'ANULADO', 'Anulado', '#991B1B', 'bi-x-circle', 16, 0, 1);

-- 6. PRIORIDADES
TRUNCATE TABLE `prioridades`;
INSERT INTO `prioridades` (`id`, `codigo`, `nombre`, `color`, `orden`) VALUES
(1, 'NORMAL', 'Normal', '#6B7280', 1),
(2, 'URGENTE', 'Urgente', '#F97316', 2),
(3, 'MUY_URGENTE', 'Muy Urgente', '#B3261E', 3);

-- 7. ROLES (Sección 9)
TRUNCATE TABLE `roles`;
INSERT INTO `roles` (`id`, `nombre`, `slug`, `descripcion`, `estado`) VALUES
(1, 'Superadministrador', 'superadmin', 'Acceso total y configuración del sistema', 1),
(2, 'Administrador', 'admin', 'Gestión institucional y usuarios', 1),
(3, 'Mesa de Partes', 'mesa_partes', 'Recepción de trámites, emisión de cargos y despacho', 1),
(4, 'Dirección General', 'direccion', 'Revisión directiva, derivación y aprobación final', 1),
(5, 'Responsable de Unidad', 'unidad', 'Recepción y atención técnica de expedientes', 1),
(6, 'Auditor', 'auditor', 'Supervisión y control en solo lectura', 1);

-- 8. PERMISOS RBAC (Sección 29)
TRUNCATE TABLE `permisos`;
INSERT INTO `permisos` (`id`, `modulo`, `nombre`, `slug`, `descripcion`) VALUES
-- Expedientes
(1, 'expedientes', 'Ver expedientes', 'expedientes.ver', 'Permiso para listar y ver detalles de expedientes'),
(2, 'expedientes', 'Crear trámites', 'expedientes.crear', 'Permiso para registrar trámites en el sistema'),
(3, 'expedientes', 'Editar trámites', 'expedientes.editar', 'Permiso para editar datos permitidos de expedientes'),
(4, 'expedientes', 'Enviar a Dirección', 'expedientes.enviar_direccion', 'Permite remitir expedientes a Dirección'),
(5, 'expedientes', 'Derivar expedientes', 'expedientes.derivar', 'Permite derivar expedientes a unidades orgánicas'),
(6, 'expedientes', 'Recepcionar en unidad', 'expedientes.recibir', 'Permiso para sellar recepción formal en unidad'),
(7, 'expedientes', 'Responder expediente', 'expedientes.responder', 'Permiso para elaborar informe y remitir a Dirección'),
(8, 'expedientes', 'Observar expediente', 'expedientes.observar', 'Permiso para emitir observaciones formales'),
(9, 'expedientes', 'Devolver expediente', 'expedientes.devolver', 'Permiso para devolver a unidad u oficina origen'),
(10, 'expedientes', 'Aprobar respuesta', 'expedientes.aprobar', 'Permiso para aprobar resoluciones e informes'),
(11, 'expedientes', 'Finalizar trámite', 'expedientes.finalizar', 'Permiso para concluir formalmente el trámite'),
(12, 'expedientes', 'Archivar trámite', 'expedientes.archivar', 'Permiso para archivar expediente'),
(13, 'expedientes', 'Anular trámite', 'expedientes.anular', 'Permiso para anular expediente'),
(14, 'expedientes', 'Descargar documentos', 'expedientes.descargar', 'Permiso para acceder y descargar adjuntos'),
-- Usuarios
(15, 'usuarios', 'Ver usuarios', 'usuarios.ver', 'Ver lista de usuarios'),
(16, 'usuarios', 'Crear usuarios', 'usuarios.crear', 'Registrar nuevos funcionarios'),
(17, 'usuarios', 'Editar usuarios', 'usuarios.editar', 'Editar datos de funcionarios'),
(18, 'usuarios', 'Gestionar roles', 'usuarios.roles', 'Asignar roles y permisos'),
-- Unidades
(19, 'unidades', 'Ver unidades', 'unidades.ver', 'Ver catálogo de unidades orgánicas'),
(20, 'unidades', 'Crear unidades', 'unidades.crear', 'Registrar unidades'),
(21, 'unidades', 'Editar unidades', 'unidades.editar', 'Editar unidades'),
-- Trámites
(22, 'tramites', 'Ver trámites', 'tramites.ver', 'Ver tipos de trámites y requisitos'),
(23, 'tramites', 'Crear trámites', 'tramites.crear', 'Registrar tipos de trámites'),
(24, 'tramites', 'Editar trámites', 'tramites.editar', 'Editar tipos de trámites'),
-- Programas
(25, 'programas', 'Ver programas', 'programas.ver', 'Ver programas de estudios'),
(26, 'programas', 'Crear programas', 'programas.crear', 'Registrar programas de estudio'),
(27, 'programas', 'Editar programas', 'programas.editar', 'Editar programas de estudio'),
-- Reportes & Auditoría
(28, 'reportes', 'Ver reportes', 'reportes.ver', 'Acceso a estadísticas y reportes'),
(29, 'reportes', 'Exportar reportes', 'reportes.exportar', 'Descarga de reportes en PDF y CSV'),
(30, 'auditoria', 'Ver auditoría', 'auditoria.ver', 'Consultar bitácora de trazabilidad'),
-- Configuración
(31, 'configuracion', 'Editar apariencia', 'configuracion.apariencia', 'Personalizar logos y colores'),
(32, 'configuracion', 'Editar general', 'configuracion.general', 'Editar datos institucionales'),
(33, 'configuracion', 'Configurar SMTP', 'configuracion.smtp', 'Ajustar servidor de correo');

-- 9. ASIGNACIÓN ROLES - PERMISOS
TRUNCATE TABLE `roles_permisos`;
-- Superadmin (todos los permisos)
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 1, `id` FROM `permisos`;

-- Administrador (todos salvo anular directo y auditoría sensible)
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 2, `id` FROM `permisos` WHERE `slug` NOT IN ('expedientes.anular');

-- Mesa de Partes
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 3, `id` FROM `permisos` WHERE `slug` IN (
    'expedientes.ver', 'expedientes.crear', 'expedientes.editar',
    'expedientes.enviar_direccion', 'expedientes.finalizar',
    'expedientes.descargar', 'tramites.ver', 'programas.ver'
);

-- Dirección General
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 4, `id` FROM `permisos` WHERE `slug` IN (
    'expedientes.ver', 'expedientes.derivar', 'expedientes.observar',
    'expedientes.devolver', 'expedientes.aprobar', 'expedientes.finalizar',
    'expedientes.archivar', 'expedientes.descargar', 'reportes.ver',
    'tramites.ver', 'programas.ver', 'unidades.ver'
);

-- Responsable de Unidad
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 5, `id` FROM `permisos` WHERE `slug` IN (
    'expedientes.ver', 'expedientes.recibir', 'expedientes.responder',
    'expedientes.observar', 'expedientes.descargar'
);

-- Auditor
INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`)
SELECT 6, `id` FROM `permisos` WHERE `slug` IN (
    'expedientes.ver', 'expedientes.descargar', 'reportes.ver',
    'reportes.exportar', 'auditoria.ver', 'tramites.ver', 'unidades.ver'
);

-- 10. USUARIOS INICIALES (Sección 45)
TRUNCATE TABLE `usuarios`;
-- Password para todos en seed: Admin123*
INSERT INTO `usuarios` (`id`, `dni`, `nombres`, `apellidos`, `username`, `email`, `password_hash`, `telefono`, `cargo`, `unidad_id`, `estado`, `debe_cambiar_password`) VALUES
(1, '00000001', 'Administrador', 'General', 'admin', 'admin@tupacamaru.edu.pe', '$2y$10$4DQSCck.FZeFShQiL8H6meM9zg/cQBsddaHDASbNsEFPWHi3gEcWu', '984000001', 'Administrador General del Sistema', NULL, 1, 1),
(2, '00000002', 'Operador', 'Mesa de Partes', 'mesapartes', 'mesadepartes@tupacamaru.edu.pe', '$2y$10$4DQSCck.FZeFShQiL8H6meM9zg/cQBsddaHDASbNsEFPWHi3gEcWu', '984000002', 'Especialista de Trámite Documentario', 1, 1, 0),
(3, '00000003', 'Director', 'Institucional', 'director', 'director@tupacamaru.edu.pe', '$2y$10$4DQSCck.FZeFShQiL8H6meM9zg/cQBsddaHDASbNsEFPWHi3gEcWu', '984000003', 'Director General IESP Túpac Amaru', 1, 1, 0),
(4, '00000004', 'Jefe', 'Unidad Académica', 'academica', 'jefe.academica@tupacamaru.edu.pe', '$2y$10$4DQSCck.FZeFShQiL8H6meM9zg/cQBsddaHDASbNsEFPWHi3gEcWu', '984000004', 'Jefe de Unidad Académica', 2, 1, 0),
(5, '00000005', 'Auditor', 'Interno', 'auditor', 'auditoria@tupacamaru.edu.pe', '$2y$10$4DQSCck.FZeFShQiL8H6meM9zg/cQBsddaHDASbNsEFPWHi3gEcWu', '984000005', 'Auditor Institucional', NULL, 1, 0);

-- 11. ASIGNACIÓN USUARIOS - ROLES
TRUNCATE TABLE `usuarios_roles`;
INSERT INTO `usuarios_roles` (`usuario_id`, `rol_id`) VALUES
(1, 1), -- admin -> Superadministrador
(2, 3), -- mesapartes -> Mesa de Partes
(3, 4), -- director -> Dirección General
(4, 5), -- academica -> Responsable de Unidad
(5, 6); -- auditor -> Auditor

-- 12. CONFIGURACIONES GENERALES Y APARIENCIA OBLIGATORIA (Sección 25, 27, 44)
TRUNCATE TABLE `configuraciones`;
INSERT INTO `configuraciones` (`clave`, `valor`, `descripcion`, `grupo`) VALUES
-- Identidad
('institucion_nombre', 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO', 'Nombre oficial de la institución', 'general'),
('institucion_nombre_corto', 'IESP Túpac Amaru - Cusco', 'Nombre corto para encabezados y cargos', 'general'),
('institucion_dependencia', 'GERENCIA REGIONAL DE EDUCACIÓN CUSCO', 'Entidad superior de adscripción', 'general'),
('institucion_resolucion', 'R.M 195-2005-ED', 'Resolución Ministerial de creación o revalidación', 'general'),
('institucion_ruc', '20165849301', 'RUC de la entidad pública', 'general'),
('institucion_direccion', 'Av. Cusco N° 456, San Sebastián, Cusco - Perú', 'Dirección física de mesa de partes', 'general'),
('institucion_telefono', '(084) 223344 - 984123456', 'Teléfono de atención al ciudadano', 'general'),
('institucion_correo', 'mesadepartes@tupacamaru.edu.pe', 'Correo electrónico oficial para trámites', 'general'),
('institucion_web', 'https://www.tupacamaru.edu.pe', 'Portal web oficial', 'general'),
('institucion_ciudad', 'Cusco', 'Ciudad sede', 'general'),
('institucion_region', 'Cusco', 'Región sede', 'general'),
('institucion_anio', 'Año del Bicentenario, de la consolidación de nuestra Independencia', 'Nombre oficial del año', 'general'),
('institucion_pie_pagina', 'IESP Túpac Amaru – Cusco | Mesa de Partes Virtual Oficial. Atención de Lunes a Viernes de 08:00 a 16:30 hrs.', 'Texto al pie de páginas y documentos', 'general'),
-- Apariencia (Paleta institucional obligatoria: Rojo, Naranja, Plomo)
('logo_principal', '', 'Ruta del logo principal institucional', 'apariencia'),
('logo_login', '', 'Ruta del logo específico para pantalla de inicio de sesión', 'apariencia'),
('logo_documentos', '', 'Ruta del logo optimizado para cargos e impresiones', 'apariencia'),
('favicon', '', 'Ruta del favicon del navegador', 'apariencia'),
('color_primario', '#B3261E', 'Rojo principal institucional', 'apariencia'),
('color_primario_oscuro', '#7F1D1D', 'Rojo oscuro para estados hover y activos', 'apariencia'),
('color_primario_suave', '#FEE2E2', 'Rojo suave para alertas y fondos destacados', 'apariencia'),
('color_secundario', '#F97316', 'Naranja principal para acciones secundarias', 'apariencia'),
('color_secundario_oscuro', '#C2410C', 'Naranja oscuro para contrastes y hovers', 'apariencia'),
('color_secundario_suave', '#FFEDD5', 'Naranja suave para estados en trámite', 'apariencia'),
('color_sidebar', '#374151', 'Plomo oscuro para barra lateral', 'apariencia'),
('color_sidebar_texto', '#F3F4F6', 'Color de texto de enlaces del sidebar', 'apariencia'),
('color_header', '#FFFFFF', 'Fondo del header superior', 'apariencia'),
('color_fondo', '#F3F4F6', 'Fondo general de la aplicación', 'apariencia'),
('color_texto_principal', '#374151', 'Plomo oscuro para textos y títulos principales', 'apariencia'),
('color_texto_secundario', '#6B7280', 'Plomo medio para textos secundarios y subtítulos', 'apariencia'),
('color_borde', '#D1D5DB', 'Plomo claro para bordes de tablas y tarjetas', 'apariencia');

-- 13. CONFIGURACIÓN SMTP INICIAL
TRUNCATE TABLE `configuracion_smtp`;
INSERT INTO `configuracion_smtp` (`id`, `host`, `puerto`, `usuario`, `password_encriptado`, `seguridad`, `remitente_email`, `remitente_nombre`, `activo`) VALUES
(1, 'smtp.gmail.com', 587, '', '', 'tls', 'mesadepartes@tupacamaru.edu.pe', 'Mesa de Partes - IESP Túpac Amaru', 0);

SET FOREIGN_KEY_CHECKS = 1;
