# PROMPT MAESTRO — SISTEMA DE MESA DE PARTES VIRTUAL
## IESP TÚPAC AMARU – CUSCO

# 1. OBJETIVO DEL PROYECTO

Desarrollar un Sistema Web de Mesa de Partes Virtual completo para el INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO.

Tecnologías obligatorias:
- PHP nativo 8.2 o superior
- Arquitectura MVC
- MySQL 8 o superior
- PDO
- HTML5
- CSS3
- JavaScript Vanilla
- Bootstrap 5 opcional
- Apache
- .htaccess
- URLs amigables

No utilizar Laravel, Symfony, CodeIgniter ni ningún framework PHP.

El sistema debe ser modular, seguro, responsivo, escalable, mantenible y preparado para producción.

# 2. IDENTIDAD INSTITUCIONAL

Institución:
INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO

Referencia institucional:
GERENCIA REGIONAL DE EDUCACIÓN CUSCO
INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO
FORMULARIO ÚNICO DE TRÁMITE
R.M 195-2005-ED

Todos estos datos deben ser editables desde Administración.

# 3. FLUJO GENERAL

USUARIO / PETICIONANTE
→ MESA DE PARTES
→ DIRECCIÓN GENERAL
→ UNIDAD / OFICINA RESPONSABLE
→ DIRECCIÓN GENERAL
→ MESA DE PARTES / RESPUESTA
→ USUARIO

Reglas:
1. Todo trámite ingresa por Mesa de Partes.
2. Mesa de Partes registra y genera el expediente.
3. Mesa de Partes remite a Dirección General.
4. Dirección General deriva a una unidad u oficina.
5. La unidad atiende y devuelve respuesta a Dirección General.
6. Dirección General aprueba, observa, devuelve, reasigna o finaliza.
7. El usuario puede consultar en qué oficina se encuentra su expediente.

# 4. UNIDADES INSTITUCIONALES

Crear inicialmente:
1. Dirección General
2. Unidad Académica
3. Secretaría Académica
4. Unidad de Bienestar
5. Unidad de Calidad
6. Oficina de Personal
7. Oficina de Administración
8. Biblioteca
9. Oficina de Abastecimiento
10. Oficina de Patrimonio

Cada unidad debe tener:
- Código
- Nombre
- Descripción
- Responsable
- Cargo
- Correo
- Teléfono
- Estado
- Orden

No eliminar físicamente unidades con historial.

# 5. FORMULARIO ÚNICO DE TRÁMITE DIGITAL

Crear un formulario público que replique funcionalmente el FUT institucional.

Campos:
- Solicito
- Sumilla
- Apellido paterno
- Apellido materno
- Nombres
- DNI
- Correo electrónico
- Dirección domiciliaria
- Número celular
- ¿Es estudiante o egresado?
- Programa de estudios
- Código
- Año de ingreso
- Año de egreso
- Tipo de petición
- Fundamento de la petición
- Documentos adjuntos

Si no es estudiante o egresado, ocultar campos académicos.

# 6. TIPOS DE PETICIÓN DEL FUT

## 1. TRÁMITES DE TITULACIÓN
1.1 Designación de Asesor
1.2 Designación de Docente Especialista
1.3 Aprobación de Trabajo de Aplicación
1.4 Fecha y Hora de Examen
1.5 Autorización Compra Formato de Título
1.6 Otorgamiento de Bachillerato
1.7 Otorgamiento de Título
1.8 Duplicado de Título

## 2. CERTIFICADO DE
2.1 Estudios
2.2 Bachillerato (Estudios)
2.3 Módulo
2.4 Idiomas

## 3. CONSTANCIA DE
3.1 Estudios
3.2 Conducta
3.3 Egresado
3.4 No Adeudo
3.5 Título en trámite

## 4. ESRT (PRÁCTICA)
4.1 Oficio de Presentación
4.2 Calificación de Informe

## 5. CARNET DE ESTUDIANTE
## 6. FICHA DE SEGUIMIENTO
## 7. LICENCIA / RESERVA DE MATRÍCULA
## 8. REINCORPORACIÓN
## 9. CONVALIDACIÓN
## 10. EXAMEN EXTRAORDINARIO
## 11. RECTIFICACIÓN DE DATOS
## 12. SÍLABO
## 13. TRASLADO INTERNO / EXTERNO
## 14. CONTRATA

## 15. JUSTIFICACIÓN DE
15.1 Tardanza
15.2 Omisión de Picado de Tarjeta
15.3 Inasistencia

Nota: el documento fuente ubica “Inasistencia” como “14.3” dentro del bloque 15. El código debe ser configurable desde administración para no depender de una numeración rígida.

## 16. PERMISO
## 17. ALQUILER DE AMBIENTES Y/O EQUIPOS
## 18. OTROS

Cada tipo de trámite debe permitir:
- Código
- Nombre
- Categoría
- Descripción
- Unidad sugerida
- Requisitos
- Plazo referencial
- Costo
- Requiere pago
- Admite presentación virtual
- Estado
- Orden

# 7. REGISTRO DEL TRÁMITE

Al enviar:
1. Validar datos.
2. Validar archivos.
3. Crear expediente.
4. Generar número único.
5. Generar código de seguimiento.
6. Guardar documentos.
7. Registrar movimiento inicial.
8. Asignar ubicación Mesa de Partes.
9. Generar cargo.
10. Mostrar confirmación.

Formato sugerido:
EXP-2026-000001

Código de seguimiento:
TA-83J7FK

# 8. CONSULTA PÚBLICA

Ruta:
/consulta

Buscar por:
- Número de expediente
- Código de seguimiento
- DNI opcional como validación adicional

Mostrar:
- Expediente
- Sumilla
- Tipo de trámite
- Fecha de ingreso
- Estado
- Oficina actual
- Última actualización
- Observación pública
- Línea de tiempo pública

No mostrar:
- Notas internas
- Auditoría
- IP
- Documentos restringidos
- Datos de otros usuarios

# 9. ROLES

SUPERADMINISTRADOR:
- Acceso total

ADMINISTRADOR:
- Acceso según permisos

MESA DE PARTES:
- Registrar trámites presenciales
- Validar trámites virtuales
- Adjuntar documentos
- Generar cargo
- Enviar a Dirección
- Registrar entrega de respuesta

DIRECCIÓN GENERAL:
- Ver todos los expedientes
- Derivar
- Reasignar
- Observar
- Devolver
- Aprobar respuestas
- Finalizar
- Archivar

RESPONSABLE DE UNIDAD:
- Ver expedientes de su unidad
- Recepcionar
- Atender
- Adjuntar
- Responder
- Remitir a Dirección

AUDITOR:
- Solo lectura

# 10. BANDEJA DE MESA DE PARTES

Estados/bandejas:
- Nuevos
- Registrados
- Pendientes de envío
- Enviados a Dirección
- Observados
- Finalizados

Acciones:
- Ver
- Editar datos permitidos
- Adjuntar
- Generar cargo
- Enviar a Dirección
- Imprimir

# 11. BANDEJA DE DIRECCIÓN

Mostrar:
- Pendientes de revisión
- Por derivar
- Derivados
- Respuestas recibidas
- Observados
- Finalizados

Acciones:
- Derivar
- Devolver
- Observar
- Reasignar
- Agregar nota interna
- Aprobar respuesta
- Finalizar
- Archivar

# 12. DERIVACIÓN

Campos:
- Unidad destino
- Responsable opcional
- Prioridad
- Observación

Al derivar:
- Insertar movimiento
- Actualizar oficina actual
- Cambiar estado
- Crear notificación
- Registrar auditoría
- Todo en una transacción MySQL

# 13. RECEPCIÓN POR UNIDAD

La unidad debe presionar:
RECEPCIONAR EXPEDIENTE

Registrar:
- Usuario
- Unidad
- Fecha
- Hora
- IP

Diferenciar DERIVADO de RECEPCIONADO.

# 14. RESPUESTA DE UNIDAD

Registrar:
- Descripción de atención
- Resultado
- Observación interna
- Observación pública
- Documento de respuesta
- Anexos

Botón:
REMITIR RESPUESTA A DIRECCIÓN GENERAL

# 15. ESTADOS

Crear:
- RECIBIDO
- REGISTRADO
- ENVIADO A DIRECCIÓN
- EN REVISIÓN
- DERIVADO
- RECEPCIONADO
- EN TRÁMITE
- PENDIENTE DE INFORMACIÓN
- OBSERVADO
- DEVUELTO
- RESPONDIDO
- EN REVISIÓN DE DIRECCIÓN
- APROBADO
- FINALIZADO
- ARCHIVADO
- ANULADO

Cada estado:
- Código
- Nombre
- Color
- Icono
- Orden
- Activo
- Visibilidad pública

# 16. PROGRAMAS DE ESTUDIOS

Crear catálogo administrable:
- Código
- Nombre
- Estado
- Orden

No hardcodear programas que no estén proporcionados.

# 17. EXPEDIENTES

Filtros:
- Expediente
- Código seguimiento
- DNI
- Solicitante
- Programa
- Tipo de petición
- Estado
- Unidad
- Prioridad
- Fecha desde
- Fecha hasta

Columnas:
- Expediente
- Fecha
- Solicitante
- Petición
- Unidad actual
- Estado
- Prioridad
- Última actualización
- Acciones

# 18. TRAZABILIDAD

Registrar:
- Expediente
- Usuario
- Unidad origen
- Unidad destino
- Tipo de movimiento
- Fecha
- Hora
- Estado anterior
- Estado nuevo
- Observación
- IP
- User Agent

Tipos:
- REGISTRO
- VALIDACION
- ENVIO_DIRECCION
- DERIVACION
- RECEPCION
- OBSERVACION
- DEVOLUCION
- RESPUESTA
- REASIGNACION
- APROBACION
- FINALIZACION
- ARCHIVO
- ANULACION

El historial nunca se elimina.

# 19. DOCUMENTOS

Formatos:
- PDF
- DOC
- DOCX
- XLS
- XLSX
- JPG
- JPEG
- PNG

Validar:
- MIME real
- Tamaño
- Nombre
- Hash SHA-256

Guardar preferentemente en:
/storage/documents/

Descargar mediante controlador con validación de permisos.

# 20. CARGO DIGITAL

Generar cargo con:
- Logo
- Nombre institucional
- Número de expediente
- Código de seguimiento
- Fecha
- Hora
- Solicitante
- DNI
- Sumilla
- Petición
- Número de adjuntos
- Estado
- QR opcional de consulta

Exportable a PDF.

# 21. DASHBOARD

Tarjetas:
- Trámites hoy
- Trámites del mes
- En Mesa de Partes
- En Dirección
- En unidades
- Pendientes
- Observados
- Finalizados
- Urgentes

Gráficos:
- Expedientes por mes
- Por unidad
- Por estado
- Por tipo de petición
- Por programa de estudios

# 22. REPORTES

- Por fecha
- Por unidad
- Por estado
- Por petición
- Por solicitante
- Por programa
- Por responsable
- Pendientes
- Atendidos
- Finalizados
- Observados
- Tiempo promedio de atención

Exportar a PDF y CSV. Excel si se incorpora una librería adecuada.

# 23. NOTIFICACIONES

Notificar:
- Nuevo expediente
- Derivación recibida
- Observación
- Devolución
- Respuesta enviada
- Respuesta recibida
- Trámite urgente
- Finalización

Campana con contador.

# 24. SMTP

Configurable desde Administración:
- Host
- Puerto
- Usuario
- Contraseña
- TLS/SSL
- Remitente
- Nombre

Correos:
- Registro de trámite
- Observación pública
- Finalización

# 25. PERSONALIZACIÓN INSTITUCIONAL

Ruta:
Administración → Apariencia

Permitir subir:
- Logo principal
- Logo de login
- Logo para documentos
- Logo claro
- Logo oscuro
- Favicon

Formatos:
PNG, JPG, JPEG, WEBP, ICO.
SVG solo con sanitización segura.

Configurar colores:
- Principal
- Secundario
- Acento
- Sidebar
- Header
- Botones
- Fondo
- Texto
- Enlaces

Paleta visual obligatoria del sistema:

El diseño debe utilizar principalmente **rojo, naranja y tonos en plomo/gris**, manteniendo una apariencia institucional, moderna y profesional.

Paleta inicial sugerida:

- Rojo principal: #B3261E
- Rojo oscuro: #7F1D1D
- Rojo suave: #FEE2E2
- Naranja principal: #F97316
- Naranja oscuro: #C2410C
- Naranja suave: #FFEDD5
- Plomo oscuro: #374151
- Plomo medio: #6B7280
- Plomo claro: #D1D5DB
- Gris muy claro: #F3F4F6
- Blanco: #FFFFFF

Aplicación visual recomendada:

- Sidebar: plomo oscuro con detalles rojos y naranjas.
- Header: blanco o plomo muy claro con acentos rojos.
- Botón principal: rojo.
- Botón secundario: naranja.
- Botones neutros: plomo.
- Badges de estados: rojo, naranja, plomo y variantes suaves.
- Fondos generales: blanco y gris muy claro.
- Tarjetas: fondo blanco, borde plomo claro y sombras suaves.
- Hover de menú: naranja o rojo oscuro.
- Encabezados importantes: rojo principal.
- Iconos destacados: naranja.
- Texto principal: plomo oscuro.
- Texto secundario: plomo medio.

Evitar una interfaz demasiado saturada. El rojo y naranja deben emplearse como colores de énfasis, mientras que los tonos plomo/gris deben equilibrar el diseño.

Los colores deben ser editables desde Administración y aplicarse mediante variables CSS dinámicas.

# 26. PREVIEW DE APARIENCIA

Vista previa en tiempo real de:
- Login
- Sidebar
- Navbar
- Botón
- Tarjeta
- Badge
- Formulario
- Página pública

Botones:
- Restablecer
- Guardar cambios

# 27. CONFIGURACIÓN INSTITUCIONAL

Campos:
- Nombre completo
- Nombre corto
- RUC
- Dirección
- Teléfono
- Correo
- Página web
- Ciudad
- Región
- Texto pie de página
- Año institucional

# 28. GESTIÓN DE USUARIOS

Campos:
- DNI
- Nombres
- Apellidos
- Usuario
- Correo
- Teléfono
- Cargo
- Unidad
- Rol
- Estado
- Contraseña
- Último acceso

No eliminar físicamente usuarios con actividad.

# 29. RBAC

Tablas:
- roles
- permisos
- usuarios_roles
- roles_permisos

Permisos ejemplo:
- expedientes.ver
- expedientes.crear
- expedientes.editar
- expedientes.derivar
- expedientes.recibir
- expedientes.responder
- expedientes.observar
- expedientes.finalizar
- expedientes.archivar
- usuarios.ver
- usuarios.crear
- usuarios.editar
- unidades.ver
- unidades.crear
- unidades.editar
- tramites.ver
- tramites.crear
- tramites.editar
- reportes.ver
- auditoria.ver
- configuracion.editar

# 30. SEGURIDAD

Aplicar:
- PDO
- Prepared Statements
- CSRF
- htmlspecialchars
- Validación frontend y backend
- Session regeneration
- HttpOnly
- Secure cuando exista HTTPS
- SameSite
- Protección XSS
- Protección SQL Injection
- Path traversal
- Rate limiting
- Bloqueo de intentos
- MIME real
- Headers de seguridad
- Logs
- Autorización en backend en cada acción

# 31. AUDITORÍA

Registrar:
- Login
- Logout
- Login fallido
- Creación
- Edición
- Derivación
- Recepción
- Respuesta
- Observación
- Finalización
- Descarga sensible
- Cambio de configuración
- Cambio de permisos

Campos:
- usuario_id
- accion
- modulo
- registro_id
- ip
- user_agent
- datos_anteriores
- datos_nuevos
- created_at

# 32. ESTRUCTURA MVC

/
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
│   │   ├── ReporteController.php
│   │   ├── ConfiguracionController.php
│   │   ├── AuditoriaController.php
│   │   └── ConsultaController.php
│   ├── Models/
│   ├── Views/
│   ├── Core/
│   ├── Middleware/
│   └── Helpers/
├── config/
├── public/
│   ├── index.php
│   ├── .htaccess
│   └── assets/
├── storage/
│   ├── documents/
│   ├── logs/
│   └── temp/
├── database/
│   ├── schema.sql
│   └── seed.sql
├── .env
├── .env.example
└── README.md

# 33. ROUTES

GET  /
GET  /tramite
POST /tramite
GET  /consulta
POST /consulta
GET  /login
POST /login
POST /logout
GET  /dashboard
GET  /expedientes
GET  /expedientes/{id}
POST /expedientes/{id}/enviar-direccion
POST /expedientes/{id}/derivar
POST /expedientes/{id}/recibir
POST /expedientes/{id}/observar
POST /expedientes/{id}/responder
POST /expedientes/{id}/finalizar
POST /expedientes/{id}/archivar

# 34. BASE DE DATOS

Crear como mínimo:
- usuarios
- roles
- permisos
- usuarios_roles
- roles_permisos
- unidades
- programas_estudio
- categorias_tramite
- tipos_tramite
- estados_expediente
- prioridades
- expedientes
- expediente_documentos
- expediente_movimientos
- notificaciones
- configuraciones
- configuracion_smtp
- auditoria
- intentos_login

# 35. TABLA EXPEDIENTES

Campos sugeridos:
- id
- numero_expediente
- codigo_seguimiento
- solicito
- sumilla
- apellido_paterno
- apellido_materno
- nombres
- dni
- es_estudiante_egresado
- programa_id
- codigo_estudiante
- anio_ingreso
- anio_egreso
- correo
- direccion_domiciliaria
- celular
- tipo_tramite_id
- fundamento_peticion
- estado_id
- prioridad_id
- unidad_actual_id
- unidad_responsable_id
- usuario_responsable_id
- fecha_ingreso
- fecha_finalizacion
- observacion_publica
- activo
- created_at
- updated_at

UNIQUE:
- numero_expediente
- codigo_seguimiento

# 36. TABLA MOVIMIENTOS

Campos:
- id
- expediente_id
- tipo_movimiento
- unidad_origen_id
- unidad_destino_id
- usuario_id
- estado_anterior_id
- estado_nuevo_id
- observacion
- es_publico
- ip
- user_agent
- created_at

# 37. TRANSACCIONES

Usar transacciones para acciones críticas:
1. Insertar movimiento.
2. Actualizar expediente.
3. Crear notificación.
4. Registrar auditoría.
5. COMMIT.
Si falla cualquier paso: ROLLBACK.

# 38. LOGIN

Debe utilizar:
- Logo configurado
- Nombre institucional
- Colores configurados
- Usuario/correo
- Contraseña
- Mostrar/ocultar contraseña
- CSRF
- Rate limit
- Bloqueo temporal
- password_hash()
- password_verify()

# 39. INTERFAZ

Sidebar:
- Dashboard
- Mesa de Partes
- Dirección General
- Expedientes
- Mi Unidad
- Notificaciones
- Reportes
- Administración
  - Usuarios
  - Roles
  - Unidades
  - Programas
  - Tipos de trámite
  - Estados
  - Apariencia
  - Configuración
- Auditoría

Ocultar opciones según permisos.

# 40. RESPONSIVE

Debe funcionar en:
- PC
- Laptop
- Tablet
- Smartphone

En móvil:
- Sidebar hamburguesa
- Tablas adaptables
- Botones táctiles
- Formularios en una columna

# 41. BÚSQUEDA Y PAGINACIÓN

Búsqueda global:
- Expediente
- Código de seguimiento
- DNI
- Solicitante
- Sumilla

Paginación backend:
- 20
- 50
- 100 registros

# 42. FECHA Y HORA

Timezone:
America/Lima

Mostrar:
dd/mm/yyyy
HH:mm

# 43. .ENV

APP_NAME="Mesa de Partes - IESP Tupac Amaru"
APP_ENV=production
APP_URL=https://dominio.edu.pe
APP_TIMEZONE=America/Lima

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=mesa_partes
DB_USERNAME=
DB_PASSWORD=

MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=

# 44. CONFIGURACIÓN DINÁMICA

Tabla configuraciones:
- institucion_nombre
- institucion_nombre_corto
- logo
- favicon
- color_primario
- color_secundario
- color_acento
- color_sidebar
- pie_pagina

No hardcodear identidad visual.

# 45. SEED INICIAL

Crear:
- Unidades institucionales
- Estados
- Prioridades
- Categorías del FUT
- Tipos de petición del FUT
- Roles
- Permisos
- Superadministrador inicial

Obligar cambio de contraseña al primer acceso.

# 46. REGLAS DE NEGOCIO

1. Todo trámite debe tener expediente.
2. Todo trámite ingresa por Mesa de Partes.
3. Todo expediente pasa por Dirección General antes de derivación ordinaria.
4. Toda unidad debe recepcionar formalmente.
5. Cada movimiento debe ser trazable.
6. Las respuestas regresan a Dirección General.
7. El usuario público puede consultar ubicación y estado.
8. No eliminar expedientes físicamente.
9. No eliminar historial.
10. No exponer archivos por URL directa.
11. Logo, favicon y colores deben configurarse desde administración.
12. Tipos de trámite y programas deben ser administrables.
13. El catálogo inicial debe reflejar el FUT institucional.
14. Cambios de configuración deben ir a auditoría.

# 47. PRUEBAS MÍNIMAS

Probar:
- Registro válido
- Campos incompletos
- Archivo inválido
- Número de expediente único
- Login correcto e incorrecto
- Usuario inactivo
- Derivación
- Recepción
- Observación
- Devolución
- Respuesta
- Finalización
- Consulta pública
- Cambio de logo
- Cambio de favicon
- Cambio de colores
- Aplicación inmediata de apariencia

# 48. FASES DE DESARROLLO

FASE 1:
MVC, Router, PDO, .env, base de datos.

FASE 2:
Login, usuarios, roles, permisos.

FASE 3:
Unidades, programas, estados, tipos de trámite.

FASE 4:
FUT digital, expedientes, adjuntos, numeración, cargo.

FASE 5:
Mesa de Partes, Dirección, derivaciones.

FASE 6:
Bandejas de unidades, recepción, respuestas.

FASE 7:
Consulta pública, línea de tiempo.

FASE 8:
Dashboard, reportes, notificaciones.

FASE 9:
Configuración, logo, favicon, colores, SMTP.

FASE 10:
Auditoría, seguridad, pruebas, optimización.

# 49. ENTREGA

Entregar:
1. Código fuente completo
2. schema.sql
3. seed.sql
4. .env.example
5. README.md
6. Manual de instalación
7. Manual de uso
8. Árbol de carpetas
9. Roles y permisos
10. Casos de prueba

# 50. INSTRUCCIÓN FINAL

Construir un sistema real, no una maqueta.

Mantener siempre operativo el flujo:

USUARIO
→ MESA DE PARTES
→ DIRECCIÓN GENERAL
→ UNIDAD / OFICINA
→ DIRECCIÓN GENERAL
→ RESPUESTA
→ USUARIO

Priorizar:
1. Seguridad
2. Trazabilidad
3. Integridad documental
4. Facilidad de uso
5. Administración dinámica
6. Diseño institucional
7. Mantenibilidad
8. Escalabilidad
