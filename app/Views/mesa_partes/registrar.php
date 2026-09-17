<?php
use App\Helpers\Csrf;
use App\Helpers\ViewHelper;
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Registro Presencial de Trámite</h3>
        <p class="text-muted mb-0">Recepción y registro de solicitudes en ventanilla física de Mesa de Partes.</p>
    </div>
    <a href="<?= ViewHelper::url('/mesa-partes') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver a Bandeja
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="<?= ViewHelper::url('/mesa-partes/registrar') ?>" method="POST" enctype="multipart/form-data">
            <?= Csrf::field() ?>

            <div class="row g-3">
                <div class="col-md-3">
                    <label for="prioridad" class="form-label fw-semibold small">Prioridad de Atención: <span class="text-danger">*</span></label>
                    <select class="form-select" id="prioridad" name="prioridad" required>
                        <option value="NORMAL" selected>Normal</option>
                        <option value="URGENTE">Urgente</option>
                        <option value="MUY_URGENTE">Muy Urgente</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="solicito" class="form-label fw-semibold small">Solicito: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="solicito" name="solicito" placeholder="Ej: Constancia de Estudios" required>
                </div>
                <div class="col-md-5">
                    <label for="sumilla" class="form-label fw-semibold small">Sumilla / Asunto: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="sumilla" name="sumilla" placeholder="Ej: Solicito constancia de estudios para trámite de beca" required>
                </div>

                <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>

                <!-- Datos del Solicitante -->
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
                    <input type="email" class="form-control" id="correo" name="correo" placeholder="correo@ejemplo.com" required>
                </div>
                <div class="col-md-3">
                    <label for="celular" class="form-label fw-semibold small">Celular / Teléfono: <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" id="celular" name="celular" placeholder="984000000" required>
                </div>
                <div class="col-md-5">
                    <label for="direccion_domiciliaria" class="form-label fw-semibold small">Dirección Domiciliaria: <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="direccion_domiciliaria" name="direccion_domiciliaria" required>
                </div>

                <div class="col-12"><hr class="my-2 text-muted opacity-25"></div>

                <!-- Condición Estudiante -->
                <div class="col-12">
                    <label class="form-label fw-semibold small d-block">Condición del Peticionante:</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="es_estudiante_egresado" id="es_estudiante_no" value="0" checked>
                        <label class="form-check-label small" for="es_estudiante_no">Público General / Externo</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="es_estudiante_egresado" id="es_estudiante_si" value="1">
                        <label class="form-check-label small" for="es_estudiante_si">Estudiante o Egresado Institucional</label>
                    </div>
                </div>

                <div id="seccion_academica" style="display: none;" class="col-12">
                    <div class="p-3 bg-light rounded border">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="programa_id" class="form-label fw-semibold small">Programa de Estudios:</label>
                                <select class="form-select" id="programa_id" name="programa_id">
                                    <option value="">-- Seleccione Programa --</option>
                                    <?php foreach ($programas as $p): ?>
                                        <option value="<?= $p['id'] ?>"><?= ViewHelper::escape($p['nombre']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="codigo_estudiante" class="form-label fw-semibold small">Código:</label>
                                <input type="text" class="form-control" id="codigo_estudiante" name="codigo_estudiante">
                            </div>
                            <div class="col-md-2">
                                <label for="anio_ingreso" class="form-label fw-semibold small">Ingreso:</label>
                                <input type="text" class="form-control" id="anio_ingreso" name="anio_ingreso">
                            </div>
                            <div class="col-md-2">
                                <label for="anio_egreso" class="form-label fw-semibold small">Egreso:</label>
                                <input type="text" class="form-control" id="anio_egreso" name="anio_egreso">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Procedimiento FUT -->
                <div class="col-12">
                    <label for="tipo_tramite_id" class="form-label fw-semibold small">Procedimiento Oficial (FUT): <span class="text-danger">*</span></label>
                    <select class="form-select" id="tipo_tramite_id" name="tipo_tramite_id" required>
                        <option value="">-- Seleccione Procedimiento --</option>
                        <?php foreach ($tramitesGrouped as $cat => $tramites): ?>
                            <optgroup label="<?= ViewHelper::escape($cat) ?>">
                                <?php foreach ($tramites as $t): ?>
                                    <option value="<?= $t['id'] ?>">
                                        <?= ViewHelper::escape($t['codigo']) ?> - <?= ViewHelper::escape($t['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </optgroup>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Fundamentación -->
                <div class="col-12">
                    <label for="fundamento_peticion" class="form-label fw-semibold small">Fundamento de la Solicitud: <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="fundamento_peticion" name="fundamento_peticion" rows="3" required placeholder="Fundamentación presentada por el ciudadano en ventanilla..."></textarea>
                </div>

                <!-- Adjuntos -->
                <div class="col-12">
                    <label for="adjuntos" class="form-label fw-semibold small">Escanear / Adjuntar Documentos:</label>
                    <input class="form-control" type="file" id="adjuntos" name="adjuntos[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <div class="form-text small">Adjunte copias de DNI, FUT físico escaneado o requisitos en PDF / imagen.</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="<?= ViewHelper::url('/mesa-partes') ?>" class="btn btn-secondary px-4">Cancelar</a>
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    <i class="bi bi-check2-circle me-1"></i> Registrar y Emitir Expediente
                </button>
            </div>
        </form>
    </div>
</div>

<script src="<?= ViewHelper::asset('js/fut.js') ?>"></script>
