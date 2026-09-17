<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Reportes Estadísticos y Métricas</h3>
        <p class="text-muted mb-0">Consolidado general de expedientes, tiempos de atención, eficiencia por unidad y descargas.</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= ViewHelper::url('/reportes/csv?' . http_build_query($filters)) ?>" class="btn btn-success fw-bold shadow-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Exportar CSV (Excel)
        </a>
        <a href="<?= ViewHelper::url('/reportes/imprimir?' . http_build_query($filters)) ?>" target="_blank" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-printer me-1"></i> Imprimir Reporte Ejecutivo
        </a>
    </div>
</div>

<!-- Filtros de Reporte -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-4 bg-light">
        <form action="<?= ViewHelper::url('/reportes') ?>" method="GET" class="row g-3">
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Desde</label>
                <input type="date" class="form-control" name="desde" value="<?= ViewHelper::escape($filters['desde']) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Hasta</label>
                <input type="date" class="form-control" name="hasta" value="<?= ViewHelper::escape($filters['hasta']) ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Unidad Orgánica</label>
                <select class="form-select" name="unidad_id">
                    <option value="">-- Todas las Unidades --</option>
                    <?php foreach ($unidades as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $filters['unidad_id'] == $u['id'] ? 'selected' : '' ?>>
                            <?= ViewHelper::escape($u['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Estado del Expediente</label>
                <select class="form-select" name="estado_id">
                    <option value="">-- Todos los Estados --</option>
                    <?php foreach ($estados as $e): ?>
                        <option value="<?= $e['id'] ?>" <?= $filters['estado_id'] == $e['id'] ? 'selected' : '' ?>>
                            <?= ViewHelper::escape($e['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-semibold small">Canal de Ingreso</label>
                <select class="form-select" name="origen">
                    <option value="">-- Todos --</option>
                    <option value="virtual" <?= $filters['origen'] === 'virtual' ? 'selected' : '' ?>>Virtual (FUT Web)</option>
                    <option value="presencial" <?= $filters['origen'] === 'presencial' ? 'selected' : '' ?>>Presencial (Ventanilla)</option>
                </select>
            </div>
            <div class="col-12 d-flex justify-content-end gap-2 pt-2">
                <a href="<?= ViewHelper::url('/reportes') ?>" class="btn btn-outline-secondary">Limpiar Filtros</a>
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="bi bi-funnel-fill me-1"></i> Aplicar Filtros
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Tarjetas de Métricas Resumen -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 border-start border-4 border-dark h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Total Expedientes</div>
                <h3 class="fw-bold my-1"><?= $metrics['total'] ?></h3>
                <small class="text-muted">En el periodo</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 border-start border-4 border-success h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Atendidos / Resueltos</div>
                <h3 class="fw-bold my-1 text-success"><?= $metrics['atendidos'] ?></h3>
                <small class="text-muted"><?= $metrics['total'] > 0 ? round(($metrics['atendidos'] / $metrics['total']) * 100) : 0 ?>% de eficacia</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 border-start border-4 border-primary h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">En Trámite</div>
                <h3 class="fw-bold my-1 text-primary"><?= $metrics['en_tramite'] ?></h3>
                <small class="text-muted">En gestión activa</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 border-start border-4 border-warning h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Observados</div>
                <h3 class="fw-bold my-1 text-warning-emphasis"><?= $metrics['observados'] ?></h3>
                <small class="text-muted">Pendiente ciudadano</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 border-start border-4 border-info h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Dentro del Plazo</div>
                <h3 class="fw-bold my-1 text-info"><?= $metrics['dentro_plazo'] ?></h3>
                <small class="text-muted">Plazo TUPA regular</small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card shadow-sm border-0 border-start border-4 border-danger h-100">
            <div class="card-body p-3">
                <div class="text-muted small fw-semibold">Plazo Vencido</div>
                <h3 class="fw-bold my-1 text-danger"><?= $metrics['vencidos'] ?></h3>
                <small class="text-muted">Requiere atención</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Carga por Unidad Orgánica -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-secondary">
                    <i class="bi bi-buildings me-2 text-primary"></i>Distribución por Unidad Orgánica
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if (empty($porUnidad)): ?>
                    <p class="text-muted text-center py-4 mb-0">No hay datos para mostrar en este rango.</p>
                <?php else: ?>
                    <?php foreach ($porUnidad as $unidadNom => $cant): 
                        $pct = $metrics['total'] > 0 ? round(($cant / $metrics['total']) * 100) : 0;
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small text-dark"><?= ViewHelper::escape($unidadNom) ?></span>
                                <span class="badge bg-light text-dark font-monospace"><?= $cant ?> (<?= $pct ?>%)</span>
                            </div>
                            <div class="progress" style="height: 7px;">
                                <div class="progress-bar" role="progressbar" style="width: <?= $pct ?>%; background-color: var(--color-primary);" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Trámites más Frecuentes -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-secondary">
                    <i class="bi bi-bar-chart-fill me-2 text-warning"></i>Trámites Más Solicitados (Top 10)
                </h6>
            </div>
            <div class="card-body p-4">
                <?php if (empty($porTramite)): ?>
                    <p class="text-muted text-center py-4 mb-0">No hay datos en el periodo seleccionado.</p>
                <?php else: ?>
                    <?php foreach ($porTramite as $tramiteNom => $cant): 
                        $pct = $metrics['total'] > 0 ? round(($cant / $metrics['total']) * 100) : 0;
                    ?>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="fw-semibold small text-dark text-truncate" style="max-width: 80%;"><?= ViewHelper::escape($tramiteNom) ?></span>
                                <span class="badge bg-warning-subtle text-warning-emphasis font-monospace"><?= $cant ?></span>
                            </div>
                            <div class="progress" style="height: 7px;">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Tabla Detallada de Expedientes -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-table me-2"></i>Detalle de Expedientes en el Periodo (<?= count($expedientes) ?>)
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom">
            <thead>
                <tr>
                    <th>N° Expediente</th>
                    <th>Fecha Ingreso</th>
                    <th>Solicitante</th>
                    <th>Procedimiento FUT</th>
                    <th>Unidad Actual</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expedientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron expedientes con los criterios seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($expedientes as $exp): ?>
                        <tr>
                            <td>
                                <a href="<?= ViewHelper::url('/expedientes/' . $exp['id']) ?>" class="fw-bold text-decoration-none" style="color: var(--color-primary);">
                                    <?= ViewHelper::escape($exp['numero_expediente']) ?>
                                </a>
                                <small class="text-muted font-monospace d-block"><?= ViewHelper::escape($exp['codigo_seguimiento']) ?></small>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?= ViewHelper::formatDate($exp['created_at']) ?></div>
                                <span class="badge bg-light text-secondary border" style="font-size: 0.7rem;">
                                    <?= strtoupper($exp['forma_presentacion'] ?? 'VIRTUAL') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold small">
                                    <?= ViewHelper::escape($exp['tipo_solicitante'] === 'juridica' ? $exp['razon_social'] : ($exp['nombres'] . ' ' . $exp['apellidos'])) ?>
                                </div>
                                <small class="text-muted font-monospace">
                                    <?= ViewHelper::escape($exp['tipo_solicitante'] === 'juridica' ? ('RUC: ' . $exp['ruc']) : ('DNI: ' . $exp['numero_documento'])) ?>
                                </small>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark text-truncate" style="max-width: 250px;">
                                    <?= ViewHelper::escape($exp['tramite_nombre'] ?? 'Trámite General') ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= ViewHelper::escape($exp['unidad_actual_nombre'] ?? 'Mesa de Partes') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: <?= ViewHelper::escape($exp['estado_color'] ?? '#6B7280') ?>; color: #FFF;">
                                    <?= ViewHelper::escape($exp['estado_nombre'] ?? 'REGISTRADO') ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border font-monospace">
                                    <?= strtoupper($exp['prioridad_nombre'] ?? 'NORMAL') ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
