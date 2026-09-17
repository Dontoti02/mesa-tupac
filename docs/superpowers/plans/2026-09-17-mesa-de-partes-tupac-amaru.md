# Sistema de Mesa de Partes Virtual - IESP Túpac Amaru Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Construir de punta a punta el Sistema Web de Mesa de Partes Virtual para el IESP Túpac Amaru – Cusco en PHP 8.2 Nativo MVC, MySQL/MariaDB con PDO, trazabilidad estricta, FUT digital, cargos imprimibles, derivaciones transaccionales, panel administrativo con RBAC y diseño institucional en variables CSS (Rojo, Naranja y Plomo).

**Architecture:** Arquitectura MVC desacoplada orientada a objetos en PHP nativo 8.2 sin frameworks externos. Front Controller (`public/index.php`) con reescritura `.htaccess` compatible con XAMPP. Enrutador HTTP nativo con soporte de rutas dinámicas y middlewares (Auth, Guest, CSRF, RBAC). Capa de acceso a datos con PDO Singleton y transacciones atómicas. Almacenamiento seguro fuera del webroot en `storage/documents/`.

**Tech Stack:** PHP 8.2.12+, MariaDB 11.8+ / MySQL 8+, PDO, Apache 2.4 mod_rewrite, HTML5, CSS3 con Custom Properties dinámicas, JavaScript Vanilla, Bootstrap 5 (vendor local) con paleta institucional.

## Global Constraints
- PHP nativo 8.2+ sin frameworks externos (sin Laravel, Symfony ni CodeIgniter).
- 100% de consultas SQL a través de PDO con sentencias preparadas y parámetros enlazados.
- Paleta obligatoria: Rojo institucional (#B3261E), Naranja (#F97316), Plomos (#374151, #6B7280, #D1D5DB, #F3F4F6) configurable vía base de datos y variables CSS.
- Zona horaria obligatoria: `America/Lima`.
- Historial y trazabilidad nunca se eliminan físicamente.
- Transacciones MySQL en derivación, recepción y respuesta.

---

### Task 1: Estructura Base, Entorno y Esquema de Base de Datos
**Files:**
- Create: `.env.example`, `.env`, `.htaccess`, `index.php`
- Create: `config/app.php`, `config/database.php`
- Create: `database/schema.sql`, `database/seed.sql`
- Create: `storage/documents/.gitkeep`, `storage/logs/.gitkeep`, `storage/temp/.gitkeep`

- [ ] **Paso 1:** Crear estructura de directorios y archivos de entorno `.env` y `.env.example`.
- [ ] **Paso 2:** Crear `.htaccess` en raíz y en `public/` para redirección transparente a `public/index.php` en XAMPP.
- [ ] **Paso 3:** Escribir el DDL completo en `database/schema.sql` (19 tablas con índices, claves foráneas y restricciones UNIQUE).
- [ ] **Paso 4:** Escribir el DML inicial en `database/seed.sql` con las 10 unidades, 16 estados, prioridades, 18 categorías de trámites y tipos del FUT, roles, permisos, superadmin inicial y configuraciones de apariencia (colores rojo/naranja/plomo).
- [ ] **Paso 5:** Ejecutar la creación e importación de la base de datos `mesa_partes_tupac` en MariaDB mediante CLI de MySQL.
- [ ] **Paso 6:** Verificar conexión exitosa y conteo de tablas creadas.
- [ ] **Paso 7:** Commit: `git add . && git commit -m "feat: setup directory structure, environment and database schema/seed"`

---

### Task 2: Núcleo MVC (Core, Enrutador, Request, Response, Sesiones y Base de Datos)
**Files:**
- Create: `app/Core/Database.php`
- Create: `app/Core/Request.php`
- Create: `app/Core/Response.php`
- Create: `app/Core/Session.php`
- Create: `app/Core/Router.php`
- Create: `app/Core/Controller.php`
- Create: `app/Core/Model.php`
- Create: `app/Core/App.php`
- Create: `public/index.php`
- Create: `app/Helpers/Csrf.php`, `app/Helpers/Validator.php`, `app/Helpers/ViewHelper.php`

- [ ] **Paso 1:** Implementar `app/Core/Database.php` con patrón Singleton para PDO y manejo de transacciones (`beginTransaction`, `commit`, `rollBack`).
- [ ] **Paso 2:** Implementar `app/Core/Request.php` con sanitización profunda de entradas y detección de métodos HTTP.
- [ ] **Paso 3:** Implementar `app/Core/Response.php` (renderizado de vistas, JSON, redirecciones seguras).
- [ ] **Paso 4:** Implementar `app/Core/Session.php` (regeneración segura, flash messages, variables de sesión).
- [ ] **Paso 5:** Implementar `app/Core/Router.php` con soporte de parámetros de ruta (`{id}`), middlewares y despacho a controladores.
- [ ] **Paso 6:** Implementar `app/Core/Controller.php` y `app/Core/Model.php` con métodos comunes de consulta.
- [ ] **Paso 7:** Implementar `app/Helpers/Csrf.php`, `Validator.php` y `ViewHelper.php`.
- [ ] **Paso 8:** Configurar `public/index.php` para inicializar el contenedor `App.php` y cargar rutas.
- [ ] **Paso 9:** Probar ruta de prueba por CLI (`php -r ...`) o curl para confirmar respuesta 200.
- [ ] **Paso 10:** Commit: `git add app public && git commit -m "feat: implement native MVC core, router, database and helpers"`

---

### Task 3: Autenticación, Seguridad y RBAC (Roles y Permisos)
**Files:**
- Create: `app/Models/Usuario.php`, `app/Models/Rol.php`, `app/Models/Permiso.php`, `app/Models/IntentoLogin.php`, `app/Models/Auditoria.php`
- Create: `app/Middleware/AuthMiddleware.php`, `app/Middleware/GuestMiddleware.php`, `app/Middleware/PermissionMiddleware.php`, `app/Middleware/CsrfMiddleware.php`
- Create: `app/Controllers/AuthController.php`
- Create: `app/Views/layouts/auth.php`, `app/Views/auth/login.php`, `app/Views/auth/cambiar_password.php`

- [ ] **Paso 1:** Implementar modelos `Usuario`, `Rol`, `Permiso` con métodos para verificar permisos de usuario (`hasPermission($slug)`), roles y autenticación.
- [ ] **Paso 2:** Implementar `IntentoLogin` para control de intentos fallidos (bloqueo tras 5 fallos durante 15 minutos).
- [ ] **Paso 3:** Implementar middlewares de autenticación, invitado, verificación de permisos RBAC y protección CSRF.
- [ ] **Paso 4:** Implementar `AuthController` con acciones `showLogin()`, `login()`, `logout()`, `showChangePassword()`, `updatePassword()`.
- [ ] **Paso 5:** Construir la vista de login responsiva con el branding institucional (logo, títulos y colores rojo/naranja/plomo).
- [ ] **Paso 6:** Probar login con credenciales correctas (`admin` / password inicial) y verificar redirección con cambio obligatorio de contraseña.
- [ ] **Paso 7:** Probar rechazo con credenciales erróneas e incremento del contador de intentos.
- [ ] **Paso 8:** Commit: `git add . && git commit -m "feat: implement authentication, RBAC system, security middlewares and login view"`

---

### Task 4: Layout Administrativo, Variables CSS Dinámicas y Apariencia
**Files:**
- Create: `public/assets/css/variables.css`, `public/assets/css/app.css`
- Create: `public/assets/js/app.js`
- Create: `app/Models/Configuracion.php`
- Create: `app/Views/layouts/app.php`
- Create: `app/Views/partials/header.php`, `app/Views/partials/sidebar.php`, `app/Views/partials/footer.php`, `app/Views/partials/alerts.php`
- Create: `app/Controllers/DashboardController.php`
- Create: `app/Views/dashboard/index.php`

- [ ] **Paso 1:** Implementar modelo `Configuracion` que cargue la paleta de colores y datos institucionales en caché/memoria.
- [ ] **Paso 2:** Crear `variables.css` inyectando dinámicamente los colores primario (#B3261E), secundario (#F97316) y tonos plomo.
- [ ] **Paso 3:** Implementar el Layout administrativo `app.php` con sidebar colapsable, menú adaptativo según roles y header con notificaciones y perfil.
- [ ] **Paso 4:** Implementar `DashboardController` calculando estadísticas reales (trámites hoy, del mes, en Mesa de Partes, en Dirección, en Unidades, pendientes, observados, finalizados).
- [ ] **Paso 5:** Diseñar la vista de Dashboard con tarjetas de métricas y gráficos estadísticos (utilizando Chart.js local o Vanilla Canvas).
- [ ] **Paso 6:** Verificar la visualización correcta del Dashboard en escritorio y vista móvil.
- [ ] **Paso 7:** Commit: `git add . && git commit -m "feat: implement admin layout, dynamic css theming and dashboard"`

---

### Task 5: Portal Ciudadano, Formulario Único de Trámite (FUT) y Cargo Digital
**Files:**
- Create: `app/Models/Expediente.php`, `app/Models/ExpedienteDocumento.php`, `app/Models/ExpedienteMovimiento.php`, `app/Models/TipoTramite.php`, `app/Models/CategoriaTramite.php`, `app/Models/ProgramaEstudio.php`, `app/Models/EstadoExpediente.php`
- Create: `app/Helpers/FileUploader.php`, `app/Helpers/CargoGenerator.php`
- Create: `app/Controllers/TramiteController.php`
- Create: `app/Views/layouts/public.php`
- Create: `app/Views/public/inicio.php`, `app/Views/public/fut.php`, `app/Views/public/confirmacion.php`, `app/Views/public/cargo.php`
- Create: `public/assets/js/fut.js`, `public/assets/css/public.css`

- [ ] **Paso 1:** Implementar lógica de generación de número único correlativo `EXP-YYYY-XXXXXX` y código de seguimiento alfanumérico aleatorio seguro `TA-XXXXXX` en `Expediente.php`.
- [ ] **Paso 2:** Implementar `FileUploader.php` para sanitización, detección de MIME real vía `finfo`, validación de extensiones (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG), cálculo de hash SHA-256 y guardado en `storage/documents/`.
- [ ] **Paso 3:** Diseñar formulario público FUT digital (`/tramite`) replicando todos los campos requeridos con campos académicos condicionales (estudiante/egresado).
- [ ] **Paso 4:** Implementar procesamiento en `TramiteController`: validación backend, registro del expediente en estado `RECIBIDO`/`REGISTRADO`, almacenamiento de adjuntos y creación del primer movimiento en la trazabilidad.
- [ ] **Paso 5:** Implementar `CargoGenerator.php` y vista `cargo.php` con presentación oficial (logos, número, fecha/hora, QR de verificación, datos del solicitante, firma institucional e impresión directa a PDF).
- [ ] **Paso 6:** Probar envío de un trámite completo, verificar archivos guardados con hash y verificar generación de cargo.
- [ ] **Paso 7:** Commit: `git add . && git commit -m "feat: implement digital FUT, file upload with sha256, unique expedientes and cargo generator"`

---

### Task 6: Consulta Pública de Trámites y Línea de Tiempo
**Files:**
- Create: `app/Controllers/ConsultaController.php`
- Create: `app/Views/public/consulta.php`

- [ ] **Paso 1:** Implementar `ConsultaController` con búsqueda por Número de Expediente o Código de Seguimiento (y DNI como validación adicional).
- [ ] **Paso 2:** Filtrar estrictamente la información pública: mostrar estado, oficina actual, fecha de ingreso, última actualización, observación pública y timeline de movimientos marcados como `es_publico = 1`.
- [ ] **Paso 3:** Ocultar rigurosamente notas internas, auditoría, direcciones IP y documentos restringidos.
- [ ] **Paso 4:** Diseñar línea de tiempo visual e intuitiva con iconos y estados coloreados según la paleta institucional.
- [ ] **Paso 5:** Probar consulta de trámite existente y prueba de error ante trámite inexistente.
- [ ] **Paso 6:** Commit: `git add . && git commit -m "feat: implement public tracking portal with secure public timeline"`

---

### Task 7: Bandeja de Mesa de Partes (Gestión, Registro Presencial y Envío a Dirección)
**Files:**
- Create: `app/Controllers/MesaPartesController.php`
- Create: `app/Views/mesa_partes/index.php`, `app/Views/mesa_partes/registrar.php`, `app/Views/mesa_partes/show.php`

- [ ] **Paso 1:** Implementar listado y filtros de trámites en Mesa de Partes (Nuevos, Registrados, Pendientes de envío, Enviados a Dirección, Observados, Finalizados).
- [ ] **Paso 2:** Implementar registro de trámites presenciales desde ventanilla con emisión inmediata de cargo.
- [ ] **Paso 3:** Implementar acción "Enviar a Dirección General": transacción PDO que actualiza estado a `ENVIADO_A_DIRECCION`, inserta movimiento `ENVIO_DIRECCION`, crea notificación y genera registro de auditoría.
- [ ] **Paso 4:** Permitir a Mesa de Partes adjuntar recaudos adicionales y registrar la entrega final de resoluciones/respuestas al solicitante.
- [ ] **Paso 5:** Probar flujo de envío a Dirección y validar que el estado cambie correctamente.
- [ ] **Paso 6:** Commit: `git add . && git commit -m "feat: implement mesa de partes inbox, in-person registration and forward to direction"`

---

### Task 8: Bandeja de Dirección General (Revisión, Derivación a Unidades y Aprobación)
**Files:**
- Create: `app/Controllers/DireccionController.php`
- Create: `app/Views/direccion/index.php`, `app/Views/direccion/derivar.php`, `app/Views/direccion/revisar_respuesta.php`

- [ ] **Paso 1:** Implementar bandeja de Dirección General: Pendientes de revisión, Por derivar, Derivados, Respuestas recibidas, Observados, Finalizados.
- [ ] **Paso 2:** Implementar acción "Derivar Expediente": modal/formulario para seleccionar Unidad destino (de las 10 unidades), prioridad (`NORMAL`, `URGENTE`, `MUY_URGENTE`), plazo referencial e instrucción/observación directiva.
- [ ] **Paso 3:** Ejecutar derivación bajo transacción MySQL atómica: insertar en `expediente_movimientos`, actualizar `unidad_actual_id` y estado a `DERIVADO`, generar notificación a la unidad receptora y registrar en auditoría.
- [ ] **Paso 4:** Implementar opciones de Observar, Devolver o Agregar Notas Internas a expedientes.
- [ ] **Paso 5:** Implementar aprobación final de respuestas remitidas por las unidades y finalización formal del expediente.
- [ ] **Paso 6:** Probar derivación hacia una unidad y verificar transacción completa.
- [ ] **Paso 7:** Commit: `git add . && git commit -m "feat: implement direccion general inbox, transactional derivation and final approval"`

---

### Task 9: Bandeja de Unidades Orgánicas ("Mi Unidad", Recepción Formal y Respuesta Técnica)
**Files:**
- Create: `app/Controllers/UnidadController.php`
- Create: `app/Views/unidad/index.php`, `app/Views/unidad/responder.php`

- [ ] **Paso 1:** Implementar bandeja "Mi Unidad": filtra exclusivamente los expedientes asignados a la unidad orgánica del usuario conectado.
- [ ] **Paso 2:** Implementar acción obligatoria **RECEPCIONAR EXPEDIENTE**: diferencia claramente entre estado `DERIVADO` y `RECEPCIONADO`, registrando de forma inmutable el usuario receptor, fecha, hora e IP.
- [ ] **Paso 3:** Implementar formulario de atención y respuesta: descripción técnica del informe, resultado, observaciones públicas/internas y carga de documento de respuesta (oficio/resolución/informe).
- [ ] **Paso 4:** Implementar botón y acción "REMITIR RESPUESTA A DIRECCIÓN GENERAL": transacción PDO que cambia estado a `RESPONDIDO`, asigna unidad actual a Dirección General, crea movimiento de respuesta y notifica al Director.
- [ ] **Paso 5:** Probar ciclo completo de recepción y remisión de respuesta desde la unidad.
- [ ] **Paso 6:** Commit: `git add . && git commit -m "feat: implement unit inbox with formal reception and technical response submission"`

---

### Task 10: Módulo de Expedientes Global, Trazabilidad y Descargas Seguras
**Files:**
- Create: `app/Controllers/ExpedienteController.php`
- Create: `app/Views/expedientes/index.php`, `app/Views/expedientes/show.php`

- [ ] **Paso 1:** Implementar listado maestro de expedientes con filtros avanzados: número, código, DNI, solicitante, programa, tipo de trámite, estado, unidad, prioridad y rango de fechas.
- [ ] **Paso 2:** Implementar paginación backend eficiente (20, 50, 100 registros).
- [ ] **Paso 3:** Implementar vista de detalle integral del expediente: datos del solicitante, requisitos cumplidos, línea de tiempo completa con notas internas y visor de documentos adjuntos.
- [ ] **Paso 4:** Implementar controlador seguro para servir documentos (`storage/documents/`) validando permisos antes de emitir los bytes del archivo con los headers HTTP correctos.
- [ ] **Paso 5:** Probar descarga de archivos con usuario autorizado y verificar rechazo en usuario sin permisos.
- [ ] **Paso 6:** Commit: `git add . && git commit -m "feat: implement master expedientes list, advanced filters and secure file download controller"`

---

### Task 11: Módulos de Administración (Usuarios, Roles, Unidades, Programas, Trámites y Estados)
**Files:**
- Create: `app/Controllers/UsuarioController.php`, `app/Controllers/AdministracionController.php`, `app/Controllers/TramiteAdminController.php`
- Create: `app/Views/administracion/usuarios.php`, `app/Views/administracion/roles.php`, `app/Views/administracion/unidades.php`, `app/Views/administracion/programas.php`, `app/Views/administracion/tramites.php`, `app/Views/administracion/estados.php`

- [ ] **Paso 1:** Implementar CRUD completo de Usuarios con asignación de Unidad y Rol (con soft-delete `activo = 0` para usuarios con actividad histórica).
- [ ] **Paso 2:** Implementar gestión de Roles y matriz interactiva de Permisos RBAC.
- [ ] **Paso 3:** Implementar CRUD de las 10 Unidades institucionales con responsable, cargo, correo y estado.
- [ ] **Paso 4:** Implementar CRUD de Programas de Estudio institucionales.
- [ ] **Paso 5:** Implementar CRUD de Tipos de Trámite del FUT (18 categorías, plazos, costos, requisitos, admisión virtual).
- [ ] **Paso 6:** Implementar administración de Estados de Expediente (colores, iconos, visibilidad pública).
- [ ] **Paso 7:** Probar creación y modificación de un tipo de trámite y un nuevo usuario.
- [ ] **Paso 8:** Commit: `git add . && git commit -m "feat: implement administration modules for users, roles, units, programs and FUT procedures"`

---

### Task 12: Configuración Institucional, Personalización de Apariencia con Live Preview y SMTP
**Files:**
- Create: `app/Controllers/ConfiguracionController.php`
- Create: `app/Helpers/Mailer.php`
- Create: `app/Views/administracion/configuracion.php`, `app/Views/administracion/apariencia.php`, `app/Views/administracion/smtp.php`
- Create: `public/assets/js/apariencia.js`

- [ ] **Paso 1:** Implementar pantalla de Configuración Institucional (Nombre oficial, RUC, dirección, teléfono, correo, lema del año, pie de página).
- [ ] **Paso 2:** Implementar pantalla de Apariencia con subida de logotipos (principal, login, documentos), favicon y configuración de paleta de colores.
- [ ] **Paso 3:** Desarrollar en `apariencia.js` el simulador en tiempo real (Live Preview) mostrando cómo afectan los colores al Login, Sidebar, Header, Botones, Tarjetas y Badges antes de guardar.
- [ ] **Paso 4:** Implementar botón "Restablecer a colores predeterminados" (Rojo #B3261E, Naranja #F97316, Plomos).
- [ ] **Paso 5:** Implementar configuración de SMTP (host, puerto, usuario, contraseña cifrada, TLS/SSL) y herramienta de envío de correo de prueba en `Mailer.php`.
- [ ] **Paso 6:** Probar cambio de colores y verificar aplicación inmediata en todo el sistema.
- [ ] **Paso 7:** Commit: `git add . && git commit -m "feat: implement institutional settings, dynamic appearance with live preview and SMTP configuration"`

---

### Task 13: Notificaciones, Reportes con Exportación (PDF y CSV) y Auditoría
**Files:**
- Create: `app/Controllers/ReporteController.php`, `app/Controllers/AuditoriaController.php`, `app/Controllers/NotificacionController.php`
- Create: `app/Views/reportes/index.php`, `app/Views/auditoria/index.php`
- Create: `app/Views/partials/notificaciones_dropdown.php`

- [ ] **Paso 1:** Implementar sistema de Notificaciones con campana y contador en el navbar, actualizable dinámicamente y con marcado de leídas.
- [ ] **Paso 2:** Implementar módulo de Reportes con múltiples filtros: por rango de fechas, unidad, estado, tipo de trámite, solicitante, y cálculo de tiempo promedio de atención.
- [ ] **Paso 3:** Implementar exportador a formato CSV descargable y vista optimizada para impresión a PDF con cabecera institucional.
- [ ] **Paso 4:** Implementar visor de Auditoría inmutable para Superadmin y Auditor: registro cronológico de logins, fallos, cambios de datos (antes y después en JSON), derivaciones, IPs y navegadores.
- [ ] **Paso 5:** Probar exportación de reporte y verificación de registros en el log de auditoría.
- [ ] **Paso 6:** Commit: `git add . && git commit -m "feat: implement real-time notifications, audit log and reports with CSV/PDF export"`

---

### Task 14: Verificación Integral de Extremo a Extremo, Manuales y Documentación de Entrega
**Files:**
- Create: `README.md`, `MANUAL_INSTALACION.md`, `MANUAL_USUARIO.md`, `CASOS_PRUEBA.md`
- Test: Ejecución de suite de validación completa del flujo.

- [ ] **Paso 1:** Ejecutar prueba de extremo a extremo: Registro en FUT público -> Recepción en Mesa de Partes -> Envío a Dirección -> Derivación a Unidad con plazo -> Recepción por la Unidad con IP -> Emisión de respuesta técnica con informe -> Aprobación por Dirección General -> Finalización del expediente -> Consulta ciudadana con visualización del timeline completo.
- [ ] **Paso 2:** Probar validaciones de seguridad: intentos de inyección SQL, bypass de CSRF, accesos a rutas restringidas sin rol adecuado y protección de archivos en `storage/documents/`.
- [ ] **Paso 3:** Elaborar `README.md` exhaustivo con arquitectura, árbol de carpetas, credenciales por defecto y roles.
- [ ] **Paso 4:** Elaborar `MANUAL_INSTALACION.md` paso a paso para despliegue en XAMPP y servidores de producción Apache/Linux.
- [ ] **Paso 5:** Elaborar `MANUAL_USUARIO.md` con guías por rol (Ciudadano, Mesa de Partes, Dirección General, Unidades Orgánicas, Administrador).
- [ ] **Paso 6:** Elaborar `CASOS_PRUEBA.md` con la matriz de pruebas ejecutadas y resultados obtenidos.
- [ ] **Paso 7:** Commit final: `git add . && git commit -m "docs: finalize user guides, installation manual and test cases for release"`
