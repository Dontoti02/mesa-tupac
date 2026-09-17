<?php
use App\Helpers\ViewHelper;
use App\Core\Session;

$user = Session::get('user');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h3 class="fw-bold mb-0" style="color: var(--color-primary);"><?= ViewHelper::escape($expediente['numero_expediente']) ?></h3>
            <span class="badge bg-warning-subtle text-warning-emphasis font-monospace fw-bold fs-6">
                <?= ViewHelper::escape($expediente['codigo_seguimiento']) ?>
            </span>
        </div>
        <p class="text-muted mb-0">Detalle integral del trámite, documentación probatoria y trazabilidad inmutable.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= ViewHelper::url('/cargo/' . $expediente['id']) ?>" target="_blank" class="btn btn-outline-secondary">
            <i class="bi bi-printer me-1"></i> Cargo Digital
        </a>
        <a href="<?= ViewHelper::url('/expedientes') ?>" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Volver a Lista
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Columna Izquierda: Información del Solicitante y Trámite -->
    <div class="col-lg-5">
        <!-- Tarjeta de Estado y Resumen -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <span class="fw-bold text-dark">ESTADO OPERATIVO</span>
                <?= ViewHelper::badgeEstado($expediente['estado_nombre'] ?? 'Recibido', $expediente['estado_color'] ?? '#6B7280', $expediente['estado_icono'] ?? 'bi-clock') ?>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Ubicación Actual:</span>
                    <strong class="text-dark"><i class="bi bi-building me-1 text-primary"></i><?= ViewHelper::escape($expediente['unidad_actual_nombre'] ?? 'Mesa de Partes') ?></strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Prioridad:</span>
                    <?= ViewHelper::badgePrioridad($expediente['prioridad_nombre'] ?? 'Normal', $expediente['prioridad_color'] ?? '#6B7280') ?>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Fecha de Ingreso:</span>
                    <span class="fw-semibold text-dark"><?= ViewHelper::formatDateTime($expediente['fecha_ingreso']) ?></span>
                </div>
                <?php if (!empty($expediente['fecha_finalizacion'])): ?>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Fecha Finalización:</span>
                        <span class="fw-bold text-success"><?= ViewHelper::formatDateTime($expediente['fecha_finalizacion']) ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tarjeta del Solicitante (FUT) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-person-lines-fill me-2" style="color: var(--color-primary);"></i> Datos del Peticionante
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small d-block">Nombre Completo:</span>
                    <strong><?= ViewHelper::escape($expediente['nombres'] . ' ' . $expediente['apellido_paterno'] . ' ' . $expediente['apellido_materno']) ?></strong>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <span class="text-muted small d-block">DNI:</span>
                        <strong class="font-monospace"><?= ViewHelper::escape($expediente['dni']) ?></strong>
                    </div>
                    <div class="col-6">
                        <span class="text-muted small d-block">Celular:</span>
                        <strong><?= ViewHelper::escape($expediente['celular']) ?></strong>
                    </div>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Correo Electrónico:</span>
                    <a href="mailto:<?= ViewHelper::escape($expediente['correo']) ?>" class="text-decoration-none"><?= ViewHelper::escape($expediente['correo']) ?></a>
                </div>
                <div class="mb-3">
                    <span class="text-muted small d-block">Dirección Domiciliaria:</span>
                    <span><?= ViewHelper::escape($expediente['direccion_domiciliaria']) ?></span>
                </div>

                <?php if (!empty($expediente['es_estudiante_egresado'])): ?>
                    <div class="p-3 bg-light rounded border small mt-3">
                        <span class="badge bg-secondary-subtle text-secondary mb-1">Condición Académica</span>
                        <div class="fw-bold"><?= ViewHelper::escape($expediente['programa_nombre'] ?? 'Programa') ?></div>
                        <div class="text-muted">Cód. Alumno: <?= ViewHelper::escape($expediente['codigo_estudiante'] ?? '-') ?> &bull; Periodo: <?= ViewHelper::escape($expediente['anio_ingreso'] ?? '-') ?> - <?= ViewHelper::escape($expediente['anio_egreso'] ?? '-') ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Documentos Adjuntos y Probatorios -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-paperclip me-2" style="color: var(--color-secondary);"></i> Archivos y Documentos
                </h6>
                <span class="badge bg-light text-dark border"><?= count($documentos) ?> archivo(s)</span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($documentos)): ?>
                    <div class="p-4 text-center text-muted small">No hay documentos adjuntos en este expediente.</div>
                <?php else: ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($documentos as $doc): ?>
                            <div class="list-group-item p-3 d-flex justify-content-between align-items-center">
                                <div class="text-truncate me-2" style="max-width: 260px;">
                                    <div class="fw-semibold text-truncate small" title="<?= ViewHelper::escape($doc['nombre_original']) ?>">
                                        <i class="bi bi-file-earmark-check-fill text-primary me-1"></i>
                                        <?= ViewHelper::escape($doc['nombre_original']) ?>
                                    </div>
                                    <div class="text-muted small" style="font-size: 0.72rem;">
                                        <?= number_format($doc['tamanio_bytes'] / (1024 * 1024), 2) ?> MB &bull; 
                                        SHA-256: <code title="<?= $doc['hash_sha256'] ?>"><?= substr($doc['hash_sha256'], 0, 10) ?>...</code>
                                    </div>
                                </div>
                                <a href="<?= ViewHelper::url('/expedientes/' . $expediente['id'] . '/documento/' . $doc['id']) ?>" class="btn btn-sm btn-outline-primary" target="_blank" title="Descarga Segura">
                                    <i class="bi bi-download"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Columna Derecha: Detalle de Solicitud y Trazabilidad Completa -->
    <div class="col-lg-7">
        <!-- Petición y Fundamentación -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-card-text me-2" style="color: var(--color-primary);"></i> Procedimiento y Petición Concreta
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <span class="text-muted small d-block">Procedimiento Oficial:</span>
                    <h5 class="fw-bold text-dark mb-1"><?= ViewHelper::escape($expediente['tramite_nombre'] ?? $expediente['solicito']) ?></h5>
                    <div class="small text-muted"><?= ViewHelper::escape($expediente['categoria_nombre'] ?? '') ?></div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">Sumilla / Asunto Declarado:</span>
                    <div class="p-2 bg-light rounded border fw-semibold small text-dark">
                        <?= ViewHelper::escape($expediente['sumilla'] ?: $expediente['solicito']) ?>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">Fundamentación de Hecho y Derecho:</span>
                    <div class="p-3 bg-light rounded border small">
                        <?= nl2br(ViewHelper::escape($expediente['fundamento_peticion'])) ?>
                    </div>
                </div>

                <?php if (!empty($expediente['observacion_publica'])): ?>
                    <div class="alert alert-info py-2 small mb-0">
                        <strong>Indicación Pública para el Administrado:</strong><br>
                        <?= nl2br(ViewHelper::escape($expediente['observacion_publica'])) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Trazabilidad Inmutable del Expediente (Sección 18) -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-2" style="color: var(--color-primary);"></i>
                    Bitácora Histórica de Trazabilidad (Nunca se elimina)
                </h6>
                <span class="badge bg-secondary-subtle text-secondary small"><?= count($movimientos) ?> evento(s)</span>
            </div>
            <div class="card-body p-4">
                <div class="timeline position-relative ps-4 py-2 border-start border-2 border-danger ms-2">
                    <?php foreach ($movimientos as $mov): ?>
                        <div class="timeline-item mb-4 position-relative">
                            <div class="position-absolute top-0 start-0 translate-middle-x rounded-circle border border-2 border-white shadow-sm d-flex align-items-center justify-content-center" 
                                 style="width: 24px; height: 24px; background-color: <?= ViewHelper::escape($mov['estado_nuevo_color'] ?? '#B3261E') ?>; color: #fff; margin-left: -16px;">
                                <i class="bi <?= ViewHelper::escape($mov['estado_nuevo_icono'] ?? 'bi-check') ?>" style="font-size: 0.7rem;"></i>
                            </div>
                            <div class="card border shadow-none bg-white p-3 ms-2">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1 mb-1">
                                    <div class="fw-bold text-dark small">
                                        <span class="badge bg-light text-dark border me-1"><?= ViewHelper::escape($mov['tipo_movimiento']) ?></span>
                                        <?= ViewHelper::escape($mov['estado_nuevo_nombre'] ?? '') ?>
                                    </div>
                                    <span class="small text-muted font-monospace" style="font-size: 0.78rem;">
                                        <i class="bi bi-clock me-1"></i><?= ViewHelper::formatDateTime($mov['created_at']) ?> hrs.
                                    </span>
                                </div>

                                <div class="small text-muted mb-2" style="font-size: 0.82rem;">
                                    <?php if (!empty($mov['unidad_origen_nombre'])): ?>
                                        <span>Origen: <strong><?= ViewHelper::escape($mov['unidad_origen_nombre']) ?></strong></span> &rarr;
                                    <?php endif; ?>
                                    <span>Destino: <strong><?= ViewHelper::escape($mov['unidad_destino_nombre'] ?? 'Mesa de Partes') ?></strong></span>
                                    <?php if (!empty($mov['usuario_nombres'])): ?>
                                        &bull; Usuario: <em><?= ViewHelper::escape($mov['usuario_nombres'] . ' ' . $mov['usuario_apellidos']) ?></em>
                                    <?php endif; ?>
                                </div>

                                <?php if (!empty($mov['observacion'])): ?>
                                    <div class="small p-2 bg-light rounded text-dark border">
                                        <?= nl2br(ViewHelper::escape($mov['observacion'])) ?>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-2 text-muted" style="font-size: 0.7rem;">
                                    IP: <code><?= ViewHelper::escape($mov['ip']) ?></code> &bull; 
                                    Visibilidad: <?= $mov['es_publico'] ? '<span class="text-success">Pública</span>' : '<span class="text-danger">Interna</span>' ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
