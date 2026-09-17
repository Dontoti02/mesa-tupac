<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Atención Técnica de Expediente</h3>
        <p class="text-muted mb-0">Elaboración de informe de respuesta y remisión a Dirección General.</p>
    </div>
    <a href="<?= ViewHelper::url('/mi-unidad') ?>" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Volver a Mi Unidad
    </a>
</div>

<div class="row g-4">
    <!-- Columna Izquierda: Datos del Expediente y Recaudos -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <span class="badge bg-secondary-subtle text-secondary small fw-bold">DATOS DEL TRÁMITE</span>
                <h5 class="fw-bold mb-0 text-dark mt-1"><?= ViewHelper::escape($expediente['numero_expediente']) ?></h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small d-block">Solicitante:</span>
                    <strong><?= ViewHelper::escape($expediente['nombres'] . ' ' . $expediente['apellido_paterno'] . ' ' . $expediente['apellido_materno']) ?></strong>
                    <div class="small text-muted">DNI: <?= ViewHelper::escape($expediente['dni']) ?> | Cel: <?= ViewHelper::escape($expediente['celular']) ?></div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">Procedimiento FUT:</span>
                    <strong class="text-primary"><?= ViewHelper::escape($expediente['tramite_nombre'] ?? $expediente['solicito']) ?></strong>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">Fundamento de la Solicitud:</span>
                    <div class="p-2 bg-light rounded small border">
                        <?= nl2br(ViewHelper::escape($expediente['fundamento_peticion'])) ?>
                    </div>
                </div>

                <?php if (!empty($expediente['es_estudiante_egresado'])): ?>
                    <div class="mb-3 p-2 bg-light rounded border small">
                        <strong>Condición Académica:</strong> <?= ViewHelper::escape($expediente['programa_nombre'] ?? '-') ?><br>
                        Código: <?= ViewHelper::escape($expediente['codigo_estudiante'] ?? '-') ?> | Ingreso: <?= ViewHelper::escape($expediente['anio_ingreso'] ?? '-') ?> - Egreso: <?= ViewHelper::escape($expediente['anio_egreso'] ?? '-') ?>
                    </div>
                <?php endif; ?>

                <div class="mb-3">
                    <span class="text-muted small d-block mb-1">Documentos Presentados por el Ciudadano:</span>
                    <?php if (empty($documentos)): ?>
                        <div class="small text-muted">No se adjuntaron documentos iniciales.</div>
                    <?php else: ?>
                        <div class="list-group list-group-flush border rounded small">
                            <?php foreach ($documentos as $doc): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div class="text-truncate me-2" style="max-width: 230px;" title="<?= ViewHelper::escape($doc['nombre_original']) ?>">
                                        <i class="bi bi-file-earmark-pdf text-danger me-1"></i>
                                        <?= ViewHelper::escape($doc['nombre_original']) ?>
                                    </div>
                                    <a href="<?= ViewHelper::url('/expedientes/' . $expediente['id'] . '/documento/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary py-0 px-2" target="_blank">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Formulario de Respuesta Técnica -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-reply-all-fill me-2" style="color: var(--color-primary);"></i> Formulario de Informe Técnico de Atención
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= ViewHelper::url('/mi-unidad/' . $expediente['id'] . '/responder') ?>" method="POST" enctype="multipart/form-data">
                    <?= Csrf::field() ?>

                    <div class="mb-3">
                        <label for="resultado" class="form-label fw-semibold small">Resultado Técnico del Trámite: <span class="text-danger">*</span></label>
                        <select class="form-select" id="resultado" name="resultado" required>
                            <option value="FAVORABLE / PROCEDENTE" selected>Favorable / Procedente (Trámite viable para aprobación)</option>
                            <option value="CON OBSERVACIONES">Con Observaciones (Requiere subsanación o complementación)</option>
                            <option value="DESFAVORABLE / IMPROCEDENTE">Desfavorable / Improcedente (No cumple causales de ley)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="descripcion" class="form-label fw-semibold small">Descripción Técnica del Informe de Atención: <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required placeholder="Detalle las acciones realizadas, verificaciones en actas, registros o bases de datos y la conclusión técnica..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="observacion_publica" class="form-label fw-semibold small">Observación / Indicación Pública (Visible para el ciudadano):</label>
                        <textarea class="form-control" id="observacion_publica" name="observacion_publica" rows="2" placeholder="Ej: Su certificado de estudios ha sido visado y emitido formalmente. Pase a recogerlo o espere notificación."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="observacion_interna" class="form-label fw-semibold small text-muted">Nota Interna (Solo para Dirección General y Personal):</label>
                        <textarea class="form-control" id="observacion_interna" name="observacion_interna" rows="2" placeholder="Comentarios confidenciales para el Director General..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="documento_respuesta" class="form-label fw-semibold small">Adjuntar Informe / Oficio / Resolución de Respuesta (PDF / Word):</label>
                        <input class="form-control" type="file" id="documento_respuesta" name="documento_respuesta" accept=".pdf,.doc,.docx">
                        <div class="form-text small">Archivo formal de respuesta emitido por la unidad orgánica.</div>
                    </div>

                    <div class="mb-4">
                        <label for="anexos" class="form-label fw-semibold small">Anexos Adicionales (Opcional):</label>
                        <input class="form-control" type="file" id="anexos" name="anexos[]" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.png">
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <a href="<?= ViewHelper::url('/mi-unidad') ?>" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold" onclick="return confirm('¿Confirma el envío de esta respuesta técnica a la Dirección General?');">
                            <i class="bi bi-send-check-fill me-2"></i> Remitir Respuesta a Dirección General
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
