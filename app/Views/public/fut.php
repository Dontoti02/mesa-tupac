<?php
use App\Helpers\Csrf;
use App\Helpers\ViewHelper;
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Encabezado del FUT -->
            <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                <div class="p-4 text-white" style="background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-hover) 100%); border-bottom: 4px solid var(--color-secondary);">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div>
                            <span class="badge bg-white text-danger fw-bold text-uppercase px-2 py-1 mb-2">FUT Digital Oficial</span>
                            <h3 class="fw-bold mb-1">Formulario Único de Trámite</h3>
                            <p class="mb-0 small text-white-50">Instituto de Educación Superior Público Túpac Amaru – Cusco &bull; R.M 195-2005-ED</p>
                        </div>
                        <div class="text-md-end">
                            <span class="badge bg-warning text-dark fw-bold px-3 py-2">
                                <i class="bi bi-shield-check me-1"></i> Trámite Seguro
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario Principal -->
            <div class="fut-form-card">
                <form action="<?= ViewHelper::url('/tramite') ?>" method="POST" enctype="multipart/form-data" id="formFut">
                    <?= Csrf::field() ?>

                    <!-- Sección 1: Petición / Sumilla -->
                    <div class="form-section-title mt-0">
                        <i class="bi bi-file-earmark-text"></i> 1. Resumen de la Solicitud
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="solicito" class="form-label fw-semibold small">Solicito: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="solicito" name="solicito" placeholder="Ej: Certificado de Estudios" required>
                        </div>
                        <div class="col-md-8">
                            <label for="sumilla" class="form-label fw-semibold small">Sumilla / Asunto: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="sumilla" name="sumilla" placeholder="Ej: Solicito otorgamiento de certificado de estudios del I al VI ciclo" required>
                        </div>
                    </div>

                    <!-- Sección 2: Datos del Peticionante -->
                    <div class="form-section-title">
                        <i class="bi bi-person-vcard"></i> 2. Datos del Peticionante (Ciudadano / Administrado)
                    </div>
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="dni" class="form-label fw-semibold small">DNI / Documento: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" id="dni" name="dni" maxlength="8" pattern="[0-9]{8}" placeholder="8 dígitos" required>
                        </div>
                        <div class="col-md-3">
                            <label for="apellido_paterno" class="form-label fw-semibold small">Apellido Paterno: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellido_paterno" name="apellido_paterno" required>
                        </div>
                        <div class="col-md-3">
                            <label for="apellido_materno" class="form-label fw-semibold small">Apellido Materno: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="apellido_materno" name="apellido_materno" required>
                        </div>
                        <div class="col-md-3">
                            <label for="nombres" class="form-label fw-semibold small">Nombres Completos: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                        </div>
                        <div class="col-md-4">
                            <label for="correo" class="form-label fw-semibold small">Correo Electrónico: <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="correo" name="correo" placeholder="ejemplo@correo.com" required>
                            <div class="form-text">Aquí recibirá las notificaciones oficiales del trámite.</div>
                        </div>
                        <div class="col-md-3">
                            <label for="celular" class="form-label fw-semibold small">Celular / WhatsApp: <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="celular" name="celular" placeholder="984000000" required>
                        </div>
                        <div class="col-md-5">
                            <label for="direccion_domiciliaria" class="form-label fw-semibold small">Dirección Domiciliaria: <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="direccion_domiciliaria" name="direccion_domiciliaria" placeholder="Av. / Jr. / Calle N°, Distrito" required>
                        </div>
                    </div>

                    <!-- Sección 3: Condición Académica -->
                    <div class="form-section-title">
                        <i class="bi bi-mortarboard"></i> 3. Condición Académica Institucional
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small d-block">¿Es usted estudiante o egresado del IESP Túpac Amaru?</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="es_estudiante_egresado" id="es_estudiante_no" value="0" checked>
                            <label class="form-check-label" for="es_estudiante_no">No (Público General / Proveedor / Personal)</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="es_estudiante_egresado" id="es_estudiante_si" value="1">
                            <label class="form-check-label" for="es_estudiante_si">Sí (Estudiante o Egresado)</label>
                        </div>
                    </div>

                    <div id="seccion_academica" style="display: none;" class="p-3 bg-light rounded border mb-3">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="programa_id" class="form-label fw-semibold small">Programa de Estudios: <span class="text-danger">*</span></label>
                                <select class="form-select" id="programa_id" name="programa_id" data-required-if-academic="true">
                                    <option value="">-- Seleccione su programa --</option>
                                    <?php foreach ($programas as $prog): ?>
                                        <option value="<?= $prog['id'] ?>"><?= ViewHelper::escape($prog['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="codigo_estudiante" class="form-label fw-semibold small">Cód. Estudiante:</label>
                                <input type="text" class="form-control font-monospace" id="codigo_estudiante" name="codigo_estudiante" placeholder="Ej: 2022-012">
                            </div>
                            <div class="col-md-2">
                                <label for="anio_ingreso" class="form-label fw-semibold small">Año Ingreso:</label>
                                <input type="text" class="form-control" id="anio_ingreso" name="anio_ingreso" maxlength="4" placeholder="Ej: 2022">
                            </div>
                            <div class="col-md-2">
                                <label for="anio_egreso" class="form-label fw-semibold small">Año Egreso:</label>
                                <input type="text" class="form-control" id="anio_egreso" name="anio_egreso" maxlength="4" placeholder="Ej: 2024">
                            </div>
                        </div>
                    </div>

                    <!-- Sección 4: Tipo de Petición FUT -->
                    <div class="form-section-title">
                        <i class="bi bi-list-check"></i> 4. Procedimiento / Tipo de Trámite
                    </div>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="tipo_tramite_id" class="form-label fw-semibold small">Seleccione el Trámite a Realizar: <span class="text-danger">*</span></label>
                            <select class="form-select form-select-lg" id="tipo_tramite_id" name="tipo_tramite_id" required>
                                <option value="">-- Seleccione el procedimiento oficial del FUT --</option>
                                <?php foreach ($tramitesGrouped as $categoriaNombre => $tramitesList): ?>
                                    <optgroup label="<?= ViewHelper::escape($categoriaNombre) ?>">
                                        <?php foreach ($tramitesList as $t): ?>
                                            <option value="<?= $t['id'] ?>" 
                                                    data-requisitos="<?= ViewHelper::escape($t['requisitos'] ?? '') ?>"
                                                    data-plazo="<?= $t['plazo_dias'] ?>"
                                                    data-costo="<?= $t['costo'] ?>">
                                                <?= ViewHelper::escape($t['codigo']) ?> - <?= ViewHelper::escape($t['nombre']) ?> 
                                                <?= (float)$t['costo'] > 0 ? '(S/ ' . number_format((float)$t['costo'], 2) . ')' : '(Gratuito)' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </optgroup>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Cuadro Informativo del Trámite Seleccionado -->
                    <div id="info_tramite_box" style="display: none;" class="alert alert-secondary mt-3 py-3 border-0 bg-light">
                        <div class="row g-2 small">
                            <div class="col-md-6">
                                <strong><i class="bi bi-info-circle text-primary me-1"></i> Requisitos sugeridos:</strong>
                                <div id="tramite_requisitos" class="text-muted mt-1"></div>
                            </div>
                            <div class="col-md-3">
                                <strong><i class="bi bi-clock text-warning me-1"></i> Plazo referencial:</strong>
                                <div id="tramite_plazo" class="text-muted mt-1"></div>
                            </div>
                            <div class="col-md-3">
                                <strong><i class="bi bi-cash-coin text-success me-1"></i> Costo por derecho:</strong>
                                <div id="tramite_costo" class="text-muted mt-1 fw-bold"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Sección 5: Fundamento de la Petición -->
                    <div class="form-section-title">
                        <i class="bi bi-chat-left-text"></i> 5. Fundamentación de la Solicitud
                    </div>
                    <div class="mb-3">
                        <label for="fundamento_peticion" class="form-label fw-semibold small">Fundamento y Justificación de los Hechos: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="fundamento_peticion" name="fundamento_peticion" rows="4" placeholder="Explique de manera clara y precisa los motivos de su petición, fundamentos de hecho y de derecho que sustentan su trámite..." required></textarea>
                    </div>

                    <!-- Sección 6: Documentos Adjuntos -->
                    <div class="form-section-title">
                        <i class="bi bi-paperclip"></i> 6. Documentos Probatorios y Requisitos Adjuntos
                    </div>
                    <div class="mb-3">
                        <label for="adjuntos" class="form-label fw-semibold small">Seleccionar Archivo(s):</label>
                        <input class="form-control" type="file" id="adjuntos" name="adjuntos[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                        <div class="form-text">
                            Formatos admitidos: <strong>PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</strong>. Tamaño máximo total: <strong>25 MB</strong>. Todos los archivos son validados por firma hash SHA-256.
                        </div>
                        <div id="lista_adjuntos"></div>
                    </div>

                    <!-- Declaración Jurada -->
                    <div class="p-3 bg-light rounded border mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="declaracion_jurada" name="declaracion_jurada" value="1" required>
                            <label class="form-check-label small" for="declaracion_jurada">
                                Declaro bajo juramento que toda la información consignada y los documentos adjuntados son auténticos y verídicos, conforme al principio de presunción de veracidad establecido en el TUO de la Ley N° 27444. Acepto recibir notificaciones en el correo electrónico indicado.
                            </label>
                        </div>
                    </div>

                    <!-- Botones de Envío -->
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 pt-2">
                        <a href="<?= ViewHelper::url('/') ?>" class="btn btn-outline-secondary px-4">
                            <i class="bi bi-x-circle me-1"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-5 fw-bold shadow-sm" id="btnSubmit">
                            <i class="bi bi-send-check me-2"></i> Registrar y Emitir Cargo Oficial
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= ViewHelper::asset('js/fut.js') ?>"></script>
