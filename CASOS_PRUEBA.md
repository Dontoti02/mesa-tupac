# Matriz de Casos de Prueba y Verificación
## Mesa de Partes Virtual - IESP Túpac Amaru Cusco

Este documento registra la matriz formal de pruebas funcionales, de seguridad, de integración y de cumplimiento normativo realizadas sobre la plataforma.

---

### Resumen de Resultados de Ejecución
- **Total de Casos de Prueba:** 14
- **Casos Aprobados:** 14 (100%)
- **Casos Fallidos:** 0 (0%)
- **Estado General:** ✅ **APROBADO PARA PRODUCCIÓN**

---

### Matriz Detallada de Casos de Prueba

| ID | Módulo / Escenario | Pasos de Prueba | Resultado Esperado | Estado |
| :--- | :--- | :--- | :--- | :---: |
| **CP-01** | **FUT Virtual:** Registro Ciudadano | 1. Acceder a `/tramite`.<br>2. Completar datos personales (DNI, Nombres, Carrera).<br>3. Seleccionar trámite del FUT y adjuntar archivo PDF.<br>4. Enviar formulario. | Se genera un registro en `expedientes`, se asigna N° correlativo anual `EXP-YYYY-XXXXXX` y clave `TA-XXXXXX`. El archivo se cifra y almacena en `storage/documents/` con hash SHA-256. | ✅ **PASS** |
| **CP-02** | **Cargo Digital:** Generación y QR | 1. Culminar el registro del CP-01.<br>2. Visualizar pantalla de confirmación.<br>3. Abrir `/cargo/{id}`.<br>4. Escanear código QR impreso con smartphone. | El cargo muestra el membrete oficial del IESP Túpac Amaru, fecha/hora, número de folios, sello institucional y un código QR funcional que redirige a la URL de consulta pública. | ✅ **PASS** |
| **CP-03** | **Consulta Pública:** Seguimiento | 1. Acceder a `/consulta`.<br>2. Ingresar N° Expediente y Código Secreto.<br>3. Enviar consulta. | Muestra la ficha oficial y la Línea de Tiempo (Timeline) con fecha, hora, estado y observaciones públicas. Oculta notas técnicas de uso interno. | ✅ **PASS** |
| **CP-04** | **Seguridad:** Rate Limiting y Brute Force | 1. Acceder a `/login`.<br>2. Ingresar credenciales inválidas 5 veces consecutivas.<br>3. Intentar un 6to inicio de sesión. | La cuenta y la IP quedan temporalmente bloqueadas durante 15 minutos informando al usuario la protección contra ataques de fuerza bruta. | ✅ **PASS** |
| **CP-05** | **Mesa de Partes:** Despacho | 1. Iniciar sesión con rol `mesa_partes`.<br>2. Acceder a `/mesa-partes`.<br>3. Seleccionar expediente pendiente.<br>4. Hacer clic en "Elevar a Dirección". | El expediente cambia atómicamente a estado `EN_TRAMITE`, la unidad actual pasa a Dirección General y se genera un movimiento en la auditoría inmutable. | ✅ **PASS** |
| **CP-06** | **Dirección General:** Derivación | 1. Iniciar sesión con rol `direccion`.<br>2. Acceder a `/direccion`.<br>3. Abrir modal "Derivar a Unidad".<br>4. Seleccionar "Unidad Académica" y colocar proveído `PROV-DIR-2026-0045`. | El expediente cambia a estado `DERIVADO`, se asigna como unidad actual a Unidad Académica y se crea una notificación interna para dicha jefatura. | ✅ **PASS** |
| **CP-07** | **Unidad Orgánica:** Recepción Formal | 1. Iniciar sesión con rol de la unidad destino.<br>2. Acceder a `/mi-unidad`.<br>3. Verificar estado "Pendiente de Recepción".<br>4. Hacer clic en "Recepcionar Formalmente". | El sistema graba la fecha/hora de recepción física/digital, el ID del funcionario y la IP. Se desbloquea el botón para emitir respuesta. | ✅ **PASS** |
| **CP-08** | **Unidad Orgánica:** Respuesta Técnica | 1. En la bandeja `/mi-unidad`, seleccionar "Responder".<br>2. Redactar el informe técnico favorable y adjuntar dictamen.<br>3. Confirmar envío. | El expediente pasa a estado `RESPONDIDO`, se adjunta el documento técnico y el expediente regresa a la bandeja de Dirección General. | ✅ **PASS** |
| **CP-09** | **Dirección General:** Aprobación Final | 1. Iniciar sesión con rol `direccion`.<br>2. Revisar respuesta de la unidad en `/direccion`.<br>3. Hacer clic en "Aprobar y Finalizar". | El expediente culmina su ciclo en estado `FINALIZADO` / `ATENDIDO`. El solicitante puede ver la resolución favorable en la consulta pública. | ✅ **PASS** |
| **CP-10** | **Seguridad:** Matriz de Roles RBAC | 1. Iniciar sesión con usuario de unidad básica.<br>2. Intentar acceder forzadamente por URL a `/administracion/usuarios` o `/administracion/roles`. | El middleware intercepta la petición y bloquea el acceso con código HTTP 403 Forbidden por carecer del rol o permiso requerido. | ✅ **PASS** |
| **CP-11** | **Apariencia:** Live Preview | 1. Iniciar sesión como administrador.<br>2. Ir a `/administracion/apariencia`.<br>3. Mover los selectores de color primario y secundario.<br>4. Verificar el panel interactivo derecho.<br>5. Guardar cambios. | El panel derecho actualiza en tiempo real botones, cabeceras y badges. Al guardar, se actualiza la BD y el archivo físico `variables.css` de manera inmediata. | ✅ **PASS** |
| **CP-12** | **Reportes:** Estadísticas y Exportación | 1. Acceder a `/reportes`.<br>2. Filtrar por fechas y unidades.<br>3. Probar botón "Exportar CSV".<br>4. Probar botón "Imprimir Reporte Ejecutivo". | El archivo CSV se descarga con cabeceras y codificación UTF-8 para Excel. La vista de impresión carga el membrete oficial, indicadores clave y bloque de firmas institucionales. | ✅ **PASS** |
| **CP-13** | **Seguridad:** Auditoría y Visor Diff | 1. Acceder a `/auditoria`.<br>2. Localizar una acción de actualización de expediente o usuario.<br>3. Hacer clic en "Ver Datos". | El modal abre el visor técnico comparativo mostrando el JSON con los datos anteriores versus los datos nuevos y el User Agent del navegador. | ✅ **PASS** |
| **CP-14** | **Comunicaciones:** Diagnóstico SMTP | 1. Acceder a `/administracion/smtp`.<br>2. Configurar servidor saliente.<br>3. Ingresar un correo en el formulario de prueba y presionar "Enviar Mensaje de Prueba". | El Helper `Mailer` establece comunicación por sockets SSL/TLS y entrega un correo con formato institucional confirmando la operatividad del servicio. | ✅ **PASS** |

---

### Evidencia de Ejecución Automatizada
El script integral `tests/test_full_workflow.php` fue ejecutado en el entorno de pruebas, verificando:
- Integridad referencial de las 19 tablas de MariaDB/MySQL.
- Inserción y lectura de caracteres en UTF-8 nativo (acentos, tildes y denominación "IESP TÚPAC AMARU – CUSCO").
- Transición secuencial de los 6 estados principales del expediente.
- Trazabilidad cronológica inmutable en la tabla `expediente_movimientos`.
- Generación de cargo oficial con código QR y validación de sellos.
- Registro del evento en la tabla `auditoria`.
