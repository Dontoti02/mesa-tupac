# Especificación de Diseño: Sistema de Mesa de Partes Virtual
## IESP Túpac Amaru – Cusco

**Fecha:** 2026-09-17  
**Estado:** Aprobado para Planificación  
**Autor:** Antigravity  

---

## 1. Visión General y Objetivos
El proyecto consiste en el desarrollo integral del **Sistema Web de Mesa de Partes Virtual** para el **Instituto de Educación Superior Público Túpac Amaru – Cusco**, bajo normativa institucional (R.M. 195-2005-ED) y estándares de interoperabilidad y seguridad del sector público peruano.

El sistema digitaliza el Formulario Único de Trámite (FUT), automatiza la generación de expedientes y códigos de seguimiento únicos, gestiona el ciclo de vida del trámite entre Mesa de Partes, Dirección General y las 10 Unidades Orgánicas, proporciona seguimiento en tiempo real al ciudadano y centraliza el control operativo con auditoría inmutable, RBAC y parametrización de identidad visual.

---

## 2. Pila Tecnológica y Restricciones
- **Lenguaje:** PHP 8.2+ Nativo (Sin frameworks como Laravel, Symfony o CodeIgniter).
- **Patrón de Arquitectura:** MVC puro (Modelo - Vista - Controlador) desacoplado, modular y orientado a objetos.
- **Motor de Base de Datos:** MySQL 8+ / MariaDB 10.4+ mediante **PDO** con Sentencias Preparadas en el 100% de las consultas.
- **Frontend:** HTML5 semántico, CSS3 moderno con variables CSS dinámicas, JavaScript Vanilla (sin dependencias complejas obligatorias). Bootstrap 5 incorporado localmente en `public/assets/vendor/` con personalización institucional estricta.
- **Servidor Web:** Apache 2.4 con módulo `mod_rewrite` activo y reglas `.htaccess` para URLs limpias.
- **Compatibilidad XAMPP:** Doble capa de enrutamiento (soporte en raíz `/mesa-tupac/` y en `VirtualHost`).
- **Almacenamiento Seguro:** Documentos guardados fuera del alcance web directo en `storage/documents/`, servidos mediante controlador con autenticación y validación de permisos.

---

## 3. Identidad Institucional y Paleta de Colores
### 3.1 Datos Institucionales
- **Institución:** INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO
- **Dependencia:** GERENCIA REGIONAL DE EDUCACIÓN CUSCO
- **Base Legal:** R.M 195-2005-ED
- **Identidad configurable:** Todos los datos (RUC, dirección, teléfono, correo, lema del año, pie de página) se administran desde la base de datos.

### 3.2 Paleta Visual Obligatoria (Variables CSS Dinámicas)
La apariencia debe transmitir sobriedad, modernidad y rigor institucional basándose en:
- **Rojo Principal:** `#B3261E` (Acciones primarias, encabezados destacados, acentos institucionales)
- **Rojo Oscuro:** `#7F1D1D` (Hover y estados activos)
- **Rojo Suave:** `#FEE2E2` (Fondos de alerta y badges urgentes)
- **Naranja Principal:** `#F97316` (Acciones secundarias, llamadas de atención, iconos destacados)
- **Naranja Oscuro:** `#C2410C` (Hover secundario)
- **Naranja Suave:** `#FFEDD5` (Avisos y estados en trámite)
- **Plomo Oscuro:** `#374151` (Texto principal, sidebar oscuro profesional)
- **Plomo Medio:** `#6B7280` (Texto secundario, bordes neutros)
- **Plomo Claro:** `#D1D5DB` (Separadores, bordes de tablas y tarjetas)
- **Gris Muy Claro:** `#F3F4F6` (Fondo de aplicación y secciones alternas)
- **Blanco Puro:** `#FFFFFF` (Fondo de tarjetas, contenido y modales)

---

## 4. Flujo de Trabajo del Trámite (State Machine)
El ciclo de vida del expediente es estrictamente secuencial y transaccional:
```
[USUARIO / CIUDADANO]
        │
        ▼ (FUT Digital / Mesa de Partes Presencial)
[MESA DE PARTES] ──────── (Revisión & Validación de Requisitos)
        │
        ▼ (Envío Formal)
[DIRECCIÓN GENERAL] ──── (Análisis & Decisión Directiva)
        │
        ├──► [DERIVAR A UNIDAD] ──► [UNIDAD ORGÁNICA] (Recepción formal)
        │                                 │
        │                                 ▼ (Atención técnica & Informe)
        │                            [RESPUESTA]
        │                                 │
        ▼                                 │
[DIRECCIÓN GENERAL] ◄─────────────────────┘
        │
        ├──► [APROBAR / FINALIZAR] ──► [MESA DE PARTES] ──► [USUARIO (Notificación & Consulta)]
        └──► [OBSERVAR / REASIGNAR]
```

### 4.1 Estados del Expediente (16 estados normativos)
1. `RECIBIDO`: Ingreso inicial por canal virtual.
2. `REGISTRADO`: Expediente formal generado con número y cargo.
3. `ENVIADO_A_DIRECCION`: Remitido por Mesa de Partes a la Dirección General.
4. `EN_REVISION`: Dirección General evaluando el trámite.
5. `DERIVADO`: Derivado a una Unidad Orgánica para su atención.
6. `RECEPCIONADO`: Unidad abrió y aceptó formalmente el expediente (sello de tiempo e IP).
7. `EN_TRAMITE`: Unidad ejecutando el trámite / informe técnico.
8. `PENDIENTE_INFORMACION`: En espera de recaudos adicionales o subsanación.
9. `OBSERVADO`: Observado con causal formal fundamentada.
10. `DEVUELTO`: Devuelto para reformulación o reasignación.
11. `RESPONDIDO`: Unidad emitió resolución/respuesta y la envió a Dirección General.
12. `EN_REVISION_DIRECCION`: Dirección General revisando la propuesta de respuesta.
13. `APROBADO`: Dirección General aprueba el informe o respuesta final.
14. `FINALIZADO`: Trámite concluido satisfactoriamente con entrega al solicitante.
15. `ARCHIVADO`: Expediente archivado en el acervo documentario.
16. `ANULADO`: Anulado por duplicidad o causal administrativa comprobada.

---

## 5. Estructura del Sistema y Directorios
```
c:/xampp/htdocs/mesa-tupac/
├── .htaccess                   # Enrutamiento raíz transparente hacia public/
├── index.php                   # Fallback directo hacia public/index.php
├── .env                        # Variables de entorno seguras (BD, App, SMTP)
├── .env.example                # Plantilla de variables
├── README.md                   # Documentación general y puesta en marcha
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ExpedienteController.php
│   │   ├── MesaPartesController.php
│   │   ├── DireccionController.php
│   │   ├── UnidadController.php
│   │   ├── UsuarioController.php
│   │   ├── TramiteController.php
│   │   ├── ProgramaController.php
│   │   ├── ReporteController.php
│   │   ├── ConfiguracionController.php
│   │   ├── AuditoriaController.php
│   │   └── ConsultaController.php
│   ├── Core/
│   │   ├── App.php             # Inicializador de la aplicación
│   │   ├── Router.php          # Enrutador REST-like nativo con expresiones regulares
│   │   ├── Controller.php      # Controlador base (render, json, redirect)
│   │   ├── Model.php           # Modelo base con PDO Singleton y Query Helpers
│   │   ├── Database.php        # Conexión PDO Singleton resiliente
│   │   ├── Request.php         # Abstracción segura de $_GET, $_POST, $_FILES y sanitización
│   │   ├── Response.php        # Emisor de headers, JSON y códigos HTTP
│   │   └── Session.php         # Manejador seguro de sesiones PHP con regeneración
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── GuestMiddleware.php
│   │   ├── RoleMiddleware.php
│   │   ├── PermissionMiddleware.php
│   │   └── CsrfMiddleware.php
│   ├── Helpers/
│   │   ├── Csrf.php            # Generación y validación de tokens anti-CSRF
│   │   ├── Validator.php       # Motor de validación de entradas de datos
│   │   ├── FileUploader.php    # Validador de MIME real, SHA-256 y guardado seguro
│   │   ├── Mailer.php          # Envío de correo SMTP con sockets PHP nativos
│   │   ├── ViewHelper.php      # Formateo de fechas (America/Lima), monedas, badges
│   │   └── CargoGenerator.php  # Generación de cargo oficial HTML imprimible
│   ├── Models/
│   │   ├── Usuario.php
│   │   ├── Rol.php
│   │   ├── Permiso.php
│   │   ├── Unidad.php
│   │   ├── ProgramaEstudio.php
│   │   ├── CategoriaTramite.php
│   │   ├── TipoTramite.php
│   │   ├── EstadoExpediente.php
│   │   ├── Prioridad.php
│   │   ├── Expediente.php
│   │   ├── ExpedienteDocumento.php
│   │   ├── ExpedienteMovimiento.php
│   │   ├── Notificacion.php
│   │   ├── Configuracion.php
│   │   ├── ConfiguracionSmtp.php
│   │   ├── Auditoria.php
│   │   └── IntentoLogin.php
│   └── Views/
│       ├── layouts/
│       │   ├── app.php         # Layout administrativo principal con sidebar dinámico
│       │   ├── public.php      # Layout para portal ciudadano y FUT
│       │   └── auth.php        # Layout para pantalla de login
│       ├── partials/
│       │   ├── header.php
│       │   ├── sidebar.php
│       │   ├── footer.php
│       │   └── alerts.php
│       ├── auth/
│       │   ├── login.php
│       │   └── cambiar_password.php
│       ├── public/
│       │   ├── inicio.php
│       │   ├── fut.php
│       │   ├── confirmacion.php
│       │   ├── cargo.php
│       │   └── consulta.php
│       ├── dashboard/
│       │   └── index.php
│       ├── expedientes/
│       │   ├── index.php
│       │   ├── show.php
│       │   ├── create.php
│       │   └── movimientos.php
│       ├── mesa_partes/
│       │   ├── index.php
│       │   └── registrar.php
│       ├── direccion/
│       │   ├── index.php
│       │   └── derivar.php
│       ├── unidad/
│       │   ├── index.php
│       │   ├── recepcion.php
│       │   └── responder.php
│       ├── usuarios/
│       │   ├── index.php
│       │   ├── create.php
│       │   └── edit.php
│       ├── administracion/
│       │   ├── roles.php
│       │   ├── unidades.php
│       │   ├── programas.php
│       │   ├── tramites.php
│       │   ├── estados.php
│       │   ├── apariencia.php
│       │   ├── configuracion.php
│       │   └── smtp.php
│       ├── reportes/
│       │   └── index.php
│       └── auditoria/
│           └── index.php
├── config/
│   ├── app.php
│   └── database.php
├── public/
│   ├── .htaccess               # Reescritura hacia index.php
│   ├── index.php               # Front Controller único
│   └── assets/
│       ├── css/
│       │   ├── variables.css   # Variables CSS institucionales dinámicas
│       │   ├── app.css         # Estilos del panel administrativo
│       │   └── public.css      # Estilos del portal y formulario FUT
│       ├── js/
│       │   ├── app.js          # Lógica interactiva del panel
│       │   ├── fut.js          # Validación en tiempo real del formulario FUT
│       │   └── apariencia.js   # Previsualización en tiempo real de colores/logo
│       ├── img/
│       │   └── default-logo.png
│       └── vendor/
│           ├── bootstrap/
│           └── fontawesome/
├── storage/
│   ├── documents/              # Archivos adjuntos cifrados/hasheados
│   ├── logs/                   # Logs de errores del sistema
│   └── temp/                   # Archivos temporales de exportación
└── database/
    ├── schema.sql              # Estructura DDL completa
    └── seed.sql                # Datos maestros y primer Superadmin
```

---

## 6. Modelo de Datos Relacional (Base de Datos)
Tablas indispensables con integridad referencial, índices y llaves foráneas:
1. `usuarios`: ID, DNI, nombres, apellidos, username, email, password_hash, telefono, cargo, unidad_id, estado, debe_cambiar_password, ultimo_acceso, timestamps.
2. `roles`: ID, nombre, slug (`superadmin`, `admin`, `mesa_partes`, `direccion`, `unidad`, `auditor`), descripcion, estado.
3. `permisos`: ID, modulo, nombre, slug (`expedientes.ver`, `expedientes.derivar`, etc.), descripcion.
4. `usuarios_roles`: usuario_id, rol_id.
5. `roles_permisos`: rol_id, permiso_id.
6. `unidades`: ID, codigo, nombre, descripcion, responsable, cargo, correo, telefono, estado, orden.
7. `programas_estudio`: ID, codigo, nombre, estado, orden.
8. `categorias_tramite`: ID, codigo, nombre, orden. (18 categorías del FUT).
9. `tipos_tramite`: ID, categoria_id, codigo, nombre, descripcion, unidad_sugerida_id, requisitos, plazo_dias, costo, requiere_pago, admite_virtual, estado, orden.
10. `estados_expediente`: ID, codigo, nombre, color, icono, orden, es_publico, activo.
11. `prioridades`: ID, codigo, nombre, color, orden (`NORMAL`, `URGENTE`, `MUY_URGENTE`).
12. `expedientes`: ID, numero_expediente (UNIQUE `EXP-YYYY-XXXXXX`), codigo_seguimiento (UNIQUE `TA-XXXXXX`), solicito, sumilla, apellido_paterno, apellido_materno, nombres, dni, es_estudiante_egresado, programa_id, codigo_estudiante, anio_ingreso, anio_egreso, correo, direccion_domiciliaria, celular, tipo_tramite_id, fundamento_peticion, estado_id, prioridad_id, unidad_actual_id, unidad_responsable_id, usuario_responsable_id, fecha_ingreso, fecha_finalizacion, observacion_publica, activo, timestamps.
13. `expediente_documentos`: ID, expediente_id, usuario_id, nombre_original, nombre_archivo, ruta_archivo, mime_type, tamanio_bytes, hash_sha256, tipo_documento (`ADJUNTO_INICIAL`, `INFORME_RESPUESTA`, `PROVEIDO`, `ANEXO`), created_at.
14. `expediente_movimientos`: ID, expediente_id, tipo_movimiento, unidad_origen_id, unidad_destino_id, usuario_id, estado_anterior_id, estado_nuevo_id, observacion, es_publico, ip, user_agent, created_at.
15. `notificaciones`: ID, usuario_id, unidad_id, titulo, mensaje, url, leido, tipo, created_at.
16. `configuraciones`: clave (PRIMARY KEY), valor, descripcion, grupo (`general`, `apariencia`, `contacto`).
17. `configuracion_smtp`: ID, host, puerto, usuario, password_encriptado, seguridad (`tls`/`ssl`), remitente_email, remitente_nombre, activo.
18. `auditoria`: ID, usuario_id, accion, modulo, registro_id, ip, user_agent, datos_anteriores (JSON), datos_nuevos (JSON), created_at.
19. `intentos_login`: ID, ip, username, fecha_hora, exito.

---

## 7. Seguridad y Buenas Prácticas
1. **Inyección SQL:** Bloqueada completamente mediante PDO y consultas parametrizadas.
2. **Cross-Site Scripting (XSS):** Todas las salidas en vistas sanitizadas con `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
3. **Cross-Site Request Forgery (CSRF):** Middleware con tokens criptográficos únicos por sesión (`bin2hex(random_bytes(32))`), verificados en todos los métodos POST.
4. **Almacenamiento y Protección Documental:**
   - Carga con validación de extensión y tipo MIME real (vía `finfo_file`).
   - Nombres aleatorizados con UUIDv4 para prevenir colisiones y directory traversal.
   - Cálculo de hash SHA-256 por cada archivo para verificar integridad.
   - Bloqueo de extensiones peligrosas (.php, .phtml, .exe, .sh, .js, .html).
   - Acceso únicamente mediante controlador de descargas seguras (`/expedientes/{id}/documento/{doc_id}`).
5. **Autenticación y Sesiones:**
   - Hashing seguro con `password_hash($pass, PASSWORD_BCRYPT)`.
   - Regeneración de ID de sesión en login (`session_regenerate_id(true)`).
   - Cookies con atributos `HttpOnly`, `SameSite=Lax`, y `Secure` condicional si HTTPS está activo.
   - Rate limiting de intentos de login con bloqueo temporal tras 5 intentos fallidos consecutivos.

---

## 8. Estrategia de Verificación y Pruebas
1. **Pruebas de Base de Datos:** Creación de esquema y carga de seeds con verificación de llaves foráneas.
2. **Pruebas de Registro FUT:** Envío con datos válidos, validación de errores por datos incompletos y archivos no permitidos.
3. **Prueba de Flujo Completo:**
   - Solicitud ciudadana -> Generación de `EXP-2026-000001` y código `TA-XXXXXX`.
   - Mesa de Partes valida y envía a Dirección General.
   - Dirección General deriva a Unidad Académica con prioridad y plazo.
   - Unidad Académica recepciona formalmente (verificación de IP y fecha/hora).
   - Unidad Académica emite respuesta con informe adjunto y remite a Dirección.
   - Dirección General aprueba y finaliza el trámite.
   - Consulta pública confirma el avance en cada etapa del timeline.
4. **Prueba de Apariencia Dinámica:** Cambio de colores en el panel y confirmación inmediata en la interfaz pública y administrativa.
5. **Prueba de Seguridad:** Intentos de acceso a rutas protegidas sin login y sin permisos correspondientes.
