<?php
use App\Helpers\ViewHelper;
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Título y Formulario de Búsqueda -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex p-3 rounded-circle mb-2" style="background-color: var(--color-primary-light); color: var(--color-primary);">
                            <i class="bi bi-search fs-2"></i>
                        </div>
                        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Seguimiento de Expediente Virtual</h3>
                        <p class="text-muted small">Consulte el estado, la oficina actual y la trazabilidad de su trámite documentario.</p>
                    </div>

                    <form action="<?= ViewHelper::url('/consulta') ?>" method="GET" class="p-3 bg-light rounded-3 border">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label for="expediente" class="form-label fw-semibold small text-uppercase">N° de Expediente</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-folder text-muted"></i></span>
                                    <input type="text" class="form-control" id="expediente" name="expediente" 
                                           value="<?= ViewHelper::escape($oldNumero) ?>" 
                                           placeholder="Ej: EXP-2026-000001">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="codigo" class="form-label fw-semibold small text-uppercase">Código de Seguimiento</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white"><i class="bi bi-key text-muted"></i></span>
                                    <input type="text" class="form-control font-monospace text-uppercase" id="codigo" name="codigo" 
                                           value="<?= ViewHelper::escape($oldCodigo) ?>" 
                                           placeholder="Ej: TA-83J7FK">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="dni" class="form-label fw-semibold small text-uppercase">DNI (Opcional)</label>
                                <input type="text" class="form-control font-monospace" id="dni" name="dni" 
                                       value="<?= ViewHelper::escape($oldDni) ?>" 
                                       maxlength="8" placeholder="8 dígitos">
                            </div>
                            <div class="col-12 text-center pt-2">
                                <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                                    <i class="bi bi-search me-1"></i> Consultar Estado
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Alerta de Error si no se encontró -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div><?= ViewHelper::escape($error) ?></div>
                </div>
            <?php endif; ?>

            <!-- Resultado del Expediente -->
            <?php if ($expediente): ?>
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <span class="text-muted small text-uppercase fw-semibold">Expediente Oficial</span>
                            <h4 class="fw-bold mb-0" style="color: var(--color-primary);"><?= ViewHelper::escape($expediente['numero_expediente']) ?></h4>
                        </div>
                        <div>
                            <?= ViewHelper::badgeEstado($expediente['estado_nombre'] ?? 'Recibido', $expediente['estado_color'] ?? '#6B7280', $expediente['estado_icono'] ?? 'bi-clock') ?>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <span class="text-muted small d-block mb-1">Petición / Asunto (FUT):</span>
                                    <div class="fw-bold fs-6 text-dark"><?= ViewHelper::escape($expediente['tramite_nombre'] ?? $expediente['solicito']) ?></div>
                                    <div class="small text-muted mt-1"><?= ViewHelper::escape($expediente['sumilla']) ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded border h-100">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Oficina / Unidad Actual:</span>
                                        <span class="fw-bold text-dark"><?= ViewHelper::escape($expediente['unidad_actual_nombre'] ?? 'Mesa de Partes') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Fecha de Ingreso:</span>
                                        <span class="fw-semibold text-dark"><?= ViewHelper::formatDateTime($expediente['fecha_ingreso']) ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Código de Consulta:</span>
                                        <span class="font-monospace fw-bold text-warning-emphasis"><?= ViewHelper::escape($expediente['codigo_seguimiento']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($expediente['observacion_publica'])): ?>
                            <div class="alert alert-info py-3 mb-4 d-flex align-items-center">
                                <i class="bi bi-info-circle-fill fs-4 me-3 text-primary"></i>
                                <div>
                                    <strong>Indicación para el solicitante:</strong><br>
                                    <?= nl2br(ViewHelper::escape($expediente['observacion_publica'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Línea de Tiempo de Atención Pública -->
                        <h5 class="fw-bold mb-3 mt-4" style="color: var(--color-primary);">
                            <i class="bi bi-diagram-3-fill me-2"></i> Línea de Tiempo del Trámite
                        </h5>

                        <?php if (empty($movimientos)): ?>
                            <div class="text-muted small py-3">No hay movimientos registrados para este expediente.</div>
                        <?php else: ?>
                            <div class="timeline position-relative ps-4 py-2 border-start border-2 border-danger ms-3">
                                <?php foreach ($movimientos as $index => $mov): ?>
                                    <div class="timeline-item mb-4 position-relative">
                                        <div class="position-absolute top-0 start-0 translate-middle-x rounded-circle border border-2 border-white shadow-sm d-flex align-items-center justify-content-center" 
                                             style="width: 26px; height: 26px; background-color: <?= ViewHelper::escape($mov['estado_nuevo_color'] ?? '#B3261E') ?>; color: #fff; margin-left: -17px;">
                                            <i class="bi <?= ViewHelper::escape($mov['estado_nuevo_icono'] ?? 'bi-check') ?> small" style="font-size: 0.75rem;"></i>
                                        </div>
                                        <div class="card border shadow-none bg-white p-3 ms-2">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1">
                                                <h6 class="fw-bold mb-0 text-dark">
                                                    <?= ViewHelper::escape($mov['tipo_movimiento']) ?> &bull; <?= ViewHelper::escape($mov['estado_nuevo_nombre'] ?? 'Actualización') ?>
                                                </h6>
                                                <span class="small text-muted font-monospace">
                                                    <i class="bi bi-clock me-1"></i><?= ViewHelper::formatDateTime($mov['created_at']) ?> hrs.
                                                </span>
                                            </div>
                                            <div class="small text-secondary mb-2">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?php if (!empty($mov['unidad_origen_nombre'])): ?>
                                                    De: <strong><?= ViewHelper::escape($mov['unidad_origen_nombre']) ?></strong> &rarr; 
                                                <?php endif; ?>
                                                En: <strong><?= ViewHelper::escape($mov['unidad_destino_nombre'] ?? 'Mesa de Partes') ?></strong>
                                            </div>
                                            <?php if (!empty($mov['observacion'])): ?>
                                                <div class="small text-dark p-2 bg-light rounded">
                                                    <?= nl2br(ViewHelper::escape($mov['observacion'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <a href="<?= ViewHelper::url('/cargo/' . $expediente['id']) ?>" target="_blank" class="btn btn-outline-secondary">
                                <i class="bi bi-printer me-1"></i> Reimprimir Cargo
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
