# Sistema de Mesa de Partes Virtual
## Instituto de Educación Superior Público "Túpac Amaru" – Cusco

Sistema Web Integral de Trámite Documentario Digital y Mesa de Partes Virtual desarrollado bajo estándares de arquitectura limpia en **PHP Nativo 8.2+ MVC (Model-View-Controller)** y motor de base de datos relacional **MySQL 8 / MariaDB**.

---

### 🏛️ Identidad Institucional y Paleta de Colores Oficial
El sistema implementa la paleta cromática reglamentaria gestionada mediante variables CSS dinámicas:
- 🔴 **Rojo Principal:** `#B3261E` (Encabezados, acentos primarios, logos)
- 🍷 **Rojo Oscuro:** `#7F1D1D` (Estados activos, botones hover)
- 🌸 **Rojo Suave:** `#FEE2E2` (Alertas, insignias y fondos destacados)
- 🟠 **Naranja Secundario:** `#F97316` (Acciones secundarias, llamadas de atención)
- 🟤 **Naranja Oscuro:** `#C2410C` (Contrastes y hovers de acento)
- 🍑 **Naranja Suave:** `#FFEDD5` (Alertas informativas de estado en trámite)
- 🔘 **Plomo / Neutro:** `#374151` / `#6B7280` / `#D1D5DB` / `#F3F4F6` (Sidebar institucional, textos, tablas y fondos)

---

### 🚀 Características y Módulos del Sistema

1. **Arquitectura Limpia Pure Native PHP MVC:**
   - Estricto desacoplamiento Modelo - Vista - Controlador sin librerías o frameworks externos pesados.
   - Enrutador HTTP regex con soporte para parámetros dinámicos (`{id}`), middlewares encadenados (`Auth`, `Guest`, `Role`, `Permission`, `Csrf`).
   - Conexión PDO Singleton con transacciones atómicas (`Database::transaction()`).

2. **Portal Ciudadano y Formulario Único de Trámite (FUT Digital):**
   - Registro interactivo con validación de solicitante (DNI / CE / RUC, filiación académica de 9 carreras).
   - Generación instantánea de número de expediente correlativo anual (`EXP-YYYY-XXXXXX`) y código único de seguimiento (`TA-XXXXXX`).
   - Carga segura de adjuntos (validación MIME real via `finfo`, hash criptográfico SHA-256).
   - **Cargo Digital Oficial Imprimible** con firma institucional y código QR de verificación pública inmediata.

3. **Consulta Ciudadana y Trazabilidad Transparente:**
   - Seguimiento público mediante N° de Expediente + Código Secreto.
   - Línea de tiempo visual (Timeline) con estados normativos, fecha y hora exacta, protegiendo notas internas de los funcionarios.

4. **Flujo Normativo Institucional Secuencial:**
   - **Mesa de Partes:** Recepción, verificación formal, registro presencial de ventanilla y elevación al Despacho de Dirección.
   - **Dirección General:** Evaluación, providencia oficial, derivación a cualquiera de las 10 Unidades Orgánicas, emisión de observaciones al ciudadano o aprobación final.
   - **Bandeja de Unidad Orgánica:** Recepción formal obligatoria (con registro de IP y timestamp) y emisión de Informe Técnico de respuesta remitido a Dirección.

5. **Panel Administrativo Integral:**
   - **Gestión de Usuarios:** Creación, edición, asignación de cargos, unidades y roles.
   - **Roles y Permisos RBAC:** Matriz granular por módulos (Mesa, Dirección, Unidades, Configuración).
   - **Catálogo de 10 Unidades Orgánicas:** Jefaturas, responsables, correos y anexos.
   - **Catálogo de Programas de Estudio:** 9 carreras oficiales del instituto.
   - **Catálogo del FUT:** 35 procedimientos organizados en 18 categorías, plazos normativos, costos y requisitos.
   - **Estados Normativos:** 16 estados del ciclo de vida documentario con selector de color e iconos.

6. **Personalización de Apariencia con Live Preview:**
   - Selector interactivo de colores en tiempo real que previsualiza Sidebar, Header, Botones e Insignias antes de guardar.
   - Sincronización instantánea con la base de datos y archivo físico `variables.css`.

7. **Reportes Ejecutivos y Exportación:**
   - Filtros por rango de fechas, unidad orgánica, estado y trámite.
   - Indicadores de eficacia, expedientes a tiempo vs. vencidos y distribución por unidad.
   - **Reporte Ejecutivo Imprimible** optimizado para hojas membretadas y guardado en PDF.
   - **Exportación a Microsoft Excel (CSV)** con BOM UTF-8.

8. **Auditoría y Seguridad Forense:**
   - Registro inmutable de cada acción en el sistema (IP, navegador, usuario, fecha y hora).
   - Visor de diferencias técnicas JSON (Estado Anterior vs. Estado Nuevo).
   - Rate limiting contra fuerza bruta (máximo 5 intentos de acceso por 15 minutos).
   - Protección CSRF universal en todos los formularios POST.

---

### 🔑 Credenciales de Acceso Iniciales para Pruebas

| Rol Institucional | Usuario | Contraseña | Unidad Asignada |
| :--- | :--- | :--- | :--- |
| **Super Administrador** | `admin` | `Admin123*` | Institucional Global |
| **Operador Mesa de Partes** | `mesa.partes` | `Mesa123*` | Mesa de Partes |
| **Director General** | `director` | `Director123*` | Dirección General |
| **Jefe Unidad Académica** | `jefe.academica` | `Unidad123*` | Unidad Académica |
| **Jefe de Administración** | `jefe.administracion` | `Admin123*` | Administración |

---

### 📂 Estructura del Directorio

```
mesa-tupac/
├── app/
│   ├── Controllers/       # Controladores MVC (Tramite, Mesa, Direccion, Unidad, Admin, etc.)
│   ├── Core/              # Núcleo nativo (App, Router, Controller, Model, Request, Response, Session, Database)
│   ├── Helpers/           # Csrf, Validator, ViewHelper, CargoGenerator, FileUploader, Mailer
│   ├── Middlewares/       # AuthMiddleware, GuestMiddleware, RoleMiddleware, PermissionMiddleware, CsrfMiddleware
│   ├── Models/            # Modelos con PDO (Expediente, Movimiento, Documento, Usuario, etc.)
│   └── Views/             # Plantillas y vistas divididas por módulos y layouts
├── config/                # Configuraciones (app.php, database.php, routes.php)
├── database/              # Esquema DDL (schema.sql) y datos semilla (seed.sql, run_seed.php)
├── public/                # DocumentRoot público con assets (css, js, img) e index.php
├── storage/               # Documentos adjuntos protegidos y logs del sistema
├── tests/                 # Scripts de prueba de integración y flujo completo
├── .env                   # Variables de entorno
├── .htaccess              # Enrutamiento de raíz a carpeta public
└── index.php              # Front-controller puente para entornos XAMPP
```

---

### 📖 Documentación Adicional
- [Manual de Instalación y Despliegue](file:///c:/xampp/htdocs/mesa-tupac/MANUAL_INSTALACION.md)
- [Manual de Usuario por Roles](file:///c:/xampp/htdocs/mesa-tupac/MANUAL_USUARIO.md)
- [Matriz de Casos de Prueba y Verificación](file:///c:/xampp/htdocs/mesa-tupac/CASOS_PRUEBA.md)
