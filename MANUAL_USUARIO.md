# Manual de Usuario por Roles
## Mesa de Partes Virtual - IESP Túpac Amaru Cusco

Este manual describe el funcionamiento y los procedimientos operativos estándar para cada uno de los roles que interactúan con la plataforma de Mesa de Partes Virtual.

---

### 1. Rol: Ciudadano / Estudiante / Solicitante Externo

#### 1.1. Presentación de Solicitud mediante FUT Digital (`/tramite`)
1. Ingrese a la dirección principal o haga clic en **"Iniciar Trámite (FUT)"**.
2. **Paso 1: Datos Personales del Solicitante**
   - Ingrese su DNI (8 dígitos).
   - Ingrese sus apellidos y nombres completos.
   - Proporcione un correo electrónico activo y número de celular (aquí recibirá las notificaciones oficiales).
   - Indique su dirección domiciliaria en Cusco o provincia.
   - Si es estudiante o egresado del instituto, marque la casilla e indique su **Programa de Estudio (Carrera)**, código de matrícula y año de ingreso/egreso.
3. **Paso 2: Datos del Procedimiento**
   - Seleccione el **Procedimiento Institucional** (ej. *Certificado Modular Oficial*, *Constancia de Egresado*, *Rectificación de Matrícula*, etc.).
   - El sistema mostrará automáticamente la descripción, el plazo normativo en días hábiles y los requisitos obligatorios.
   - Escriba el **Asunto / Resumen (Sumilla)** y el **Fundamento de la Petición** detallando los hechos que sustentan su solicitud.
4. **Paso 3: Documentación Adjunta**
   - Adjunte los archivos sustentatorios en formato PDF, Word o imágenes (máximo 25MB por archivo).
5. **Paso 4: Declaración Jurada y Envío**
   - Marque la casilla de declaración jurada sobre la veracidad de la información y haga clic en **"Presentar Trámite Oficialmente"**.

#### 1.2. Obtención del Cargo Digital con Código QR
- Al enviar la solicitud, se generará de forma automática su **Cargo Digital Oficial**.
- En la pantalla de confirmación podrá visualizar:
  - **Número de Expediente:** Formato `EXP-YYYY-XXXXXX` (ej. `EXP-2026-000001`).
  - **Código Secreto de Seguimiento:** Formato `TA-XXXXXX` (ej. `TA-SBY9QA`).
  - **Código QR:** Puede escanearlo con la cámara de cualquier teléfono móvil para abrir directamente el estado del trámite.
  - Haga clic en **"Imprimir / Descargar Cargo Oficial (PDF)"** para guardarlo en su dispositivo.

#### 1.3. Consulta y Seguimiento en Línea (`/consulta`)
1. Ingrese al enlace **"Consultar Expediente"**.
2. Digite el **Número de Expediente** y el **Código Secreto**.
3. El sistema mostrará la ficha del trámite con una **Línea de Tiempo (Timeline)** que indica:
   - Estado actual (Registrado, En Trámite, Derivado, Atendido, Observado, etc.).
   - Fecha y hora exacta de cada movimiento.
   - Unidad orgánica donde se encuentra el expediente.
   - Observaciones públicas oficiales emitidas por la institución.

---

### 2. Rol: Operador de Mesa de Partes (`mesa_partes`)

#### 2.1. Bandeja de Entrada de Mesa de Partes (`/mesa-partes`)
- La bandeja clasifica automáticamente los expedientes en pestañas:
  - **Por Enviar a Dirección:** Expedientes nuevos que requieren revisión formal.
  - **Enviados a Dirección:** Trámites que ya fueron elevados al Despacho.
  - **Observados:** Expedientes con observaciones pendientes de subsanación.
  - **Finalizados:** Expedientes con resolución o entrega concluida.

#### 2.2. Registro Presencial de Ventanilla (`/mesa-partes/registrar`)
- Cuando un solicitante acude físicamente a la ventanilla del instituto con documentos en papel:
  1. Haga clic en **"Registrar Trámite Presencial"**.
  2. Ingrese los datos del usuario, seleccione el trámite del catálogo y digitalice o adjunte los folios físicos recibidos.
  3. Indique el número de folios y guarde el registro. El sistema emitirá el Cargo Digital físico para entregarlo impreso al ciudadano.

#### 2.3. Despacho y Envío a Dirección General
1. En la lista de expedientes pendientes, haga clic en el botón **"Elevar a Dirección"**.
2. Confirme el envío. El expediente cambiará a estado `EN_TRAMITE` y pasará de inmediato a la bandeja del Director General.

---

### 3. Rol: Director General (`direccion`)

#### 3.1. Bandeja de Dirección General (`/direccion`)
- Esta bandeja recibe todos los expedientes elevados por Mesa de Partes y los informes de retorno emitidos por las Unidades Orgánicas.
- Permite 3 acciones decisorias clave sobre cada expediente:

#### 3.2. Derivación a Unidad Orgánica con Proveído
1. Haga clic en **"Derivar a Unidad"**.
2. Seleccione la unidad de destino institucional (ej. *Unidad Académica*, *Área de Administración*, *Secretaría Académica*, *Jefatura de Producción*, etc.).
3. Escriba el **Número de Proveído Oficial** (ej. `PROV-DIR-2026-0045`) y las instrucciones de atención.
4. Haga clic en **"Confirmar Derivación"**. El expediente cambiará a estado `DERIVADO` y aparecerá en la bandeja de la unidad seleccionada.

#### 3.3. Emisión de Observación Formal al Solicitante
- Si al expediente le falta algún requisito de ley o pago de tasa:
  1. Haga clic en **"Observar Trámite"**.
  2. Indique el motivo claro de la observación y el plazo para subsanar.
  3. El expediente pasará a estado `OBSERVADO` y se notificará automáticamente al correo del solicitante.

#### 3.4. Aprobación y Finalización
- Cuando la unidad orgánica competente emita su informe técnico favorable:
  1. El Director revisa el informe y hace clic en **"Aprobar y Finalizar"**.
  2. Puede adjuntar la Resolución Directoral o constancia oficial finalizada.
  3. El estado cambiará a `FINALIZADO` / `ATENDIDO`, concluyendo el ciclo del trámite.

---

### 4. Rol: Jefe de Unidad Orgánica (`/mi-unidad`)

#### 4.1. Recepción Formal Obligatoria
- Por estricta normativa de auditoría, ningún funcionario puede responder o manipular un trámite hasta que lo recepcione formalmente.
1. Ingrese a **"Mi Unidad Orgánica"**.
2. Los expedientes derivados aparecerán en estado `PENDIENTE DE RECEPCIÓN`.
3. Haga clic en el botón **"Recepcionar Formalmente"**.
4. El sistema registrará la firma digital de recepción con:
   - Fecha y hora exacta.
   - Nombres y cargo del funcionario receptor.
   - Dirección IP del equipo de cómputo institucional.

#### 4.2. Emisión de Informe Técnico y Respuesta
1. Una vez recepcionado el expediente, haga clic en **"Emitir Respuesta / Informe"**.
2. Redacte el análisis técnico de su área y especifique el número de documento de respuesta (ej. `INFORME-012-2026-UA`).
3. Adjunte los archivos resultantes (actas de notas, informes pedagógicos, dictámenes).
4. Haga clic en **"Remitir Respuesta a Dirección General"**.
5. El expediente cambiará a estado `RESPONDIDO` y retornará a la bandeja de Dirección para su firma y expedición final.

---

### 5. Rol: Administrador del Sistema (`admin` / `superadmin`)

#### 5.1. Usuarios y Seguridad RBAC
- **Usuarios (`/administracion/usuarios`):** Registro de nuevos funcionarios, asignación de cuentas institucionales, reseteo de claves y asociación a su unidad orgánica correspondiente.
- **Roles y Permisos (`/administracion/roles`):** Matriz de permisos por módulos con activación mediante interruptores.

#### 5.2. Catálogos Maestros
- **Unidades Orgánicas (`/administracion/unidades`):** Configuración de las 10 dependencias del instituto, jefes titulares, correos y orden de aparición.
- **Programas de Estudio (`/administracion/programas`):** Mantenimiento de las 9 carreras profesionales.
- **Catálogo de Trámites FUT (`/administracion/tramites`):** Modificación de plazos máximos normativos (TUPA), requisitos, costos y vinculación con la unidad sugerida.
- **Estados de Trámite (`/administracion/estados`):** Editor de los 16 estados, asignación de colores hexadecimales y visibilidad ciudadana.

#### 5.3. Apariencia y Colores con Live Preview (`/administracion/apariencia`)
- Permite ajustar la paleta institucional (Rojos, Naranjas, Plomos).
- El panel derecho actualiza en tiempo real la interfaz para verificar el contraste antes de guardar.
- Incluye botón para restaurar la paleta reglamentaria con un solo clic.

#### 5.4. Servidor SMTP (`/administracion/smtp`)
- Configuración de host, puerto y credenciales para el envío de notificaciones automáticas por correo electrónico al ciudadano.
- Incluye herramienta de prueba diagnóstica para enviar un correo de verificación en tiempo real.

#### 5.5. Inteligencia y Auditoría Forense
- **Reportes (`/reportes`):** Estadísticas de expedientes atendidos, en trámite, vencidos y distribución por área. Exportación directa a Excel (CSV) y generación de Reporte Ejecutivo Membretado para impresión en PDF.
- **Auditoría (`/auditoria`):** Registro inmutable con inspección de diferencias técnicas JSON (antes y después de cada modificación), dirección IP y navegador utilizado.
