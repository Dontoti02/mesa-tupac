<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <span class="badge bg-secondary-subtle text-secondary small text-uppercase fw-bold mb-1">
            <?= ViewHelper::escape($unidad['codigo'] ?? 'UNIDAD') ?> &bull; <?= ViewHelper::escape($unidad['cargo'] ?? 'Oficina') ?>
        </span>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);"><?= ViewHelper::escape($unidad['nombre'] ?? 'Mi Unidad') ?></h3>
        <p class="text-muted mb-0">Recepción formal con marca de tiempo e IP y emisión de respuestas técnicas para Dirección General.</p>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
        <ul class="nav nav-tabs card-header-tabs border-bottom-0">
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'por_recepcionar' ? 'active fw-bold text-danger' : 'text-muted' ?>" href="<?= ViewHelper::url('/mi-unidad?tab=por_recepcionar') ?>">
                    <i class="bi bi-box-arrow-in-down me-1"></i> Por Recepcionar
                    <span class="badge bg-danger rounded-pill ms-1"><?= $counts['por_recepcionar'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'en_tramite' ? 'active fw-bold text-primary' : 'text-muted' ?>" href="<?= ViewHelper::url('/mi-unidad?tab=en_tramite') ?>">
                    <i class="bi bi-gear-wide-connected me-1"></i> En Trámite / Recepcionados
                    <span class="badge bg-primary rounded-pill ms-1"><?= $counts['en_tramite'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'respondidos' ? 'active fw-bold text-success' : 'text-muted' ?>" href="<?= ViewHelper::url('/mi-unidad?tab=respondidos') ?>">
                    <i class="bi bi-check2-all me-1"></i> Respuestas Enviadas
                    <span class="badge bg-success rounded-pill ms-1"><?= $counts['respondidos'] ?></span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Filtros -->
    <div class="card-body bg-light border-top border-bottom py-3">
        <form action="<?= ViewHelper::url('/mi-unidad') ?>" method="GET" class="row g-2">
            <input type="hidden" name="tab" value="<?= ViewHelper::escape($currentTab) ?>">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" name="q" value="<?= ViewHelper::escape($search) ?>" placeholder="Buscar por expediente, DNI, solicitante...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                <?php if (!empty($search)): ?>
                    <a href="<?= ViewHelper::url('/mi-unidad?tab=' . $currentTab) ?>" class="btn btn-outline-secondary">Limpiar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom border-0">
            <thead>
                <tr>
                    <th>N° Expediente</th>
                    <th>Fecha Ingreso</th>
                    <th>Solicitante</th>
                    <th>Procedimiento (FUT)</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th class="text-end">Acción Requerida</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expedientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-folder2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No hay expedientes en esta bandeja.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($expedientes as $exp): ?>
                        <tr>
                            <td>
                                <a href="<?= ViewHelper::url('/expedientes/' . $exp['id']) ?>" class="fw-bold text-decoration-none" style="color: var(--color-primary);">
                                    <?= ViewHelper::escape($exp['numero_expediente']) ?>
                                </a>
                                <div class="small text-muted font-monospace"><?= ViewHelper::escape($exp['codigo_seguimiento']) ?></div>
                            </td>
                            <td><?= ViewHelper::formatDateTime($exp['fecha_ingreso']) ?></td>
                            <td>
                                <div class="fw-semibold text-dark"><?= ViewHelper::escape($exp['nombres'] . ' ' . $exp['apellido_paterno']) ?></div>
                                <div class="small text-muted">DNI: <?= ViewHelper::escape($exp['dni']) ?></div>
                            </td>
                            <td>
                                <div class="text-truncate fw-semibold" style="max-width: 220px;" title="<?= ViewHelper::escape($exp['tramite_nombre'] ?? $exp['solicito']) ?>">
                                    <?= ViewHelper::escape($exp['tramite_nombre'] ?? $exp['solicito']) ?>
                                </div>
                                <div class="small text-muted text-truncate" style="max-width: 220px;"><?= ViewHelper::escape($exp['sumilla']) ?></div>
                            </td>
                            <td>
                                <?= ViewHelper::badgeEstado($exp['estado_nombre'] ?? 'Recibido', $exp['estado_color'] ?? '#6B7280') ?>
                            </td>
                            <td>
                                <?= ViewHelper::badgePrioridad($exp['prioridad_nombre'] ?? 'Normal', $exp['prioridad_color'] ?? '#6B7280') ?>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="<?= ViewHelper::url('/expedientes/' . $exp['id']) ?>" class="btn btn-outline-secondary" title="Ver Detalle">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <?php if ($exp['estado_codigo'] === 'DERIVADO'): ?>
                                        <form action="<?= ViewHelper::url('/mi-unidad/' . $exp['id'] . '/recepcionar') ?>" method="POST" class="d-inline" onsubmit="return confirm('¿Confirma la recepción formal de este expediente? Se estampará su usuario, fecha, hora e IP.');">
                                            <?= Csrf::field() ?>
                                            <button type="submit" class="btn btn-warning text-dark fw-bold" style="background-color: var(--color-secondary); border-color: var(--color-secondary); color: #fff !important;">
                                                <i class="bi bi-check2-square me-1"></i> Recepcionar Expediente
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if (in_array($exp['estado_codigo'], ['RECEPCIONADO', 'EN_TRAMITE'])): ?>
                                        <a href="<?= ViewHelper::url('/mi-unidad/' . $exp['id'] . '/responder') ?>" class="btn btn-primary fw-bold">
                                            <i class="bi bi-pencil-square me-1"></i> Atender / Responder
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
