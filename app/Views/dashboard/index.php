<?php
use App\Helpers\ViewHelper;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Panel de Control Institucional</h3>
        <p class="text-muted mb-0">Resumen operativo de expedientes y estado documental en tiempo real.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= ViewHelper::url('/expedientes') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-folder2-open me-1"></i> Ver Todos
        </a>
        <a href="<?= ViewHelper::url('/mesa-partes/registrar') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle-fill me-1"></i> Registrar Presencial
        </a>
    </div>
</div>

<!-- 1. Tarjetas KPI -->
<div class="row g-3 mb-4">
    <!-- Hoy -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator" style="background-color: var(--color-primary);"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Trámites Hoy</span>
                    <h2 class="fw-bold mb-0 mt-1" style="color: var(--color-primary);"><?= $kpi['hoy'] ?></h2>
                </div>
                <div class="kpi-icon" style="background-color: var(--color-primary-light); color: var(--color-primary);">
                    <i class="bi bi-calendar-check"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Ingresos en ventanilla y virtual</div>
        </div>
    </div>

    <!-- Mes -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator" style="background-color: var(--color-primary);"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Trámites del Mes</span>
                    <h2 class="fw-bold mb-0 mt-1"><?= $kpi['mes'] ?></h2>
                </div>
                <div class="kpi-icon" style="background-color: #F3F4F6; color: var(--color-dark);">
                    <i class="bi bi-calendar3"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Total acumulado del periodo</div>
        </div>
    </div>

    <!-- En Mesa de Partes -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator bg-info"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">En Mesa de Partes</span>
                    <h2 class="fw-bold mb-0 mt-1 text-info"><?= $kpi['mesa_partes'] ?></h2>
                </div>
                <div class="kpi-icon bg-info-subtle text-info">
                    <i class="bi bi-inbox"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Pendientes de derivación inicial</div>
        </div>
    </div>

    <!-- En Dirección General -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator" style="background-color: var(--color-secondary);"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">En Dirección General</span>
                    <h2 class="fw-bold mb-0 mt-1" style="color: var(--color-secondary);"><?= $kpi['direccion'] ?></h2>
                </div>
                <div class="kpi-icon" style="background-color: var(--color-secondary-light); color: var(--color-secondary);">
                    <i class="bi bi-diagram-3"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Para derivación o aprobación final</div>
        </div>
    </div>

    <!-- En Unidades Orgánicas -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator bg-teal" style="background-color: #0D9488;"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">En Unidades</span>
                    <h2 class="fw-bold mb-0 mt-1" style="color: #0D9488;"><?= $kpi['unidades'] ?></h2>
                </div>
                <div class="kpi-icon" style="background-color: #CCFBF1; color: #0D9488;">
                    <i class="bi bi-building"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">En proceso de atención e informe</div>
        </div>
    </div>

    <!-- Observados -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator bg-danger"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Observados</span>
                    <h2 class="fw-bold mb-0 mt-1 text-danger"><?= $kpi['observados'] ?></h2>
                </div>
                <div class="kpi-icon bg-danger-subtle text-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Requieren subsanación documental</div>
        </div>
    </div>

    <!-- Finalizados -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator bg-success"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Finalizados</span>
                    <h2 class="fw-bold mb-0 mt-1 text-success"><?= $kpi['finalizados'] ?></h2>
                </div>
                <div class="kpi-icon bg-success-subtle text-success">
                    <i class="bi bi-check2-all"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Con resolución o respuesta emitida</div>
        </div>
    </div>

    <!-- Urgentes -->
    <div class="col-xl-3 col-sm-6">
        <div class="card card-kpi p-3">
            <div class="kpi-indicator bg-warning"></div>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted small fw-semibold text-uppercase">Urgentes / Prioritarios</span>
                    <h2 class="fw-bold mb-0 mt-1 text-warning"><?= $kpi['urgentes'] ?></h2>
                </div>
                <div class="kpi-icon bg-warning-subtle text-warning">
                    <i class="bi bi-fire"></i>
                </div>
            </div>
            <div class="mt-2 small text-muted">Prioridad alta y plazos breves</div>
        </div>
    </div>
</div>

<!-- 2. Gráficos y Distribución -->
<div class="row g-4 mb-4">
    <!-- Distribución por Unidad -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bar-chart-fill me-2" style="color: var(--color-primary);"></i> Carga de Trabajo por Unidad
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($datosPorUnidad)): ?>
                    <div class="text-center py-4 text-muted small">No hay datos de distribución por unidad todavía.</div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($datosPorUnidad as $unidad): ?>
                            <div>
                                <div class="d-flex justify-content-between small fw-semibold mb-1">
                                    <span><?= ViewHelper::escape($unidad['nombre']) ?></span>
                                    <span><?= (int)$unidad['cantidad'] ?> trámite(s)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= min(100, max(5, (int)$unidad['cantidad'] * 15)) ?>%; background-color: var(--color-primary);" 
                                         aria-valuenow="<?= (int)$unidad['cantidad'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Distribución por Estado -->
    <div class="col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-pie-chart-fill me-2" style="color: var(--color-secondary);"></i> Expedientes por Estado
                </h6>
            </div>
            <div class="card-body">
                <?php if (empty($datosPorEstado)): ?>
                    <div class="text-center py-4 text-muted small">No hay estados registrados actualmente.</div>
                <?php else: ?>
                    <div class="d-flex flex-column gap-3">
                        <?php foreach ($datosPorEstado as $estado): ?>
                            <div>
                                <div class="d-flex justify-content-between small fw-semibold mb-1">
                                    <span class="d-flex align-items-center gap-1">
                                        <span class="d-inline-block rounded-circle" style="width: 10px; height: 10px; background-color: <?= ViewHelper::escape($estado['color']) ?>;"></span>
                                        <?= ViewHelper::escape($estado['nombre']) ?>
                                    </span>
                                    <span><?= (int)$estado['cantidad'] ?> expediente(s)</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?= min(100, max(5, (int)$estado['cantidad'] * 15)) ?>%; background-color: <?= ViewHelper::escape($estado['color']) ?>;" 
                                         aria-valuenow="<?= (int)$estado['cantidad'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 3. Tabla de Últimos Expedientes Registrados -->
<div class="card table-custom border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-clock-history me-2 text-primary"></i> Últimos Trámites Registrados
        </h6>
        <a href="<?= ViewHelper::url('/expedientes') ?>" class="btn btn-sm btn-outline-secondary">
            Ver Registro Histórico Completo
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Expediente</th>
                    <th>Fecha Ingreso</th>
                    <th>Solicitante</th>
                    <th>Petición (FUT)</th>
                    <th>Ubicación Actual</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th class="text-end">Acción</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ultimosExpedientes)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Aún no se han registrado expedientes en el sistema.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ultimosExpedientes as $exp): ?>
                        <tr>
                            <td>
                                <a href="<?= ViewHelper::url('/expedientes/' . $exp['id']) ?>" class="fw-bold text-decoration-none" style="color: var(--color-primary);">
                                    <?= ViewHelper::escape($exp['numero_expediente']) ?>
                                </a>
                                <div class="small text-muted font-monospace"><?= ViewHelper::escape($exp['codigo_seguimiento']) ?></div>
                            </td>
                            <td>
                                <?= ViewHelper::formatDateTime($exp['fecha_ingreso']) ?>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= ViewHelper::escape($exp['nombres'] . ' ' . $exp['apellido_paterno']) ?></div>
                                <div class="small text-muted">DNI: <?= ViewHelper::escape($exp['dni']) ?></div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="<?= ViewHelper::escape($exp['tramite_nombre'] ?? $exp['solicito']) ?>">
                                    <?= ViewHelper::escape($exp['tramite_nombre'] ?? $exp['solicito']) ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i><?= ViewHelper::escape($exp['unidad_nombre'] ?? 'Sin asignar') ?>
                                </span>
                            </td>
                            <td>
                                <?= ViewHelper::badgeEstado($exp['estado_nombre'] ?? 'Recibido', $exp['estado_color'] ?? '#6B7280') ?>
                            </td>
                            <td>
                                <?= ViewHelper::badgePrioridad($exp['prioridad_nombre'] ?? 'Normal', $exp['prioridad_color'] ?? '#6B7280') ?>
                            </td>
                            <td class="text-end">
                                <a href="<?= ViewHelper::url('/expedientes/' . $exp['id']) ?>" class="btn btn-sm btn-outline-primary" title="Ver detalle del expediente">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
