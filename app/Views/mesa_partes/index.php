<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Bandeja de Mesa de Partes</h3>
        <p class="text-muted mb-0">Recepción, verificación y despacho oficial de expedientes documentarios.</p>
    </div>
    <div>
        <a href="<?= ViewHelper::url('/mesa-partes/registrar') ?>" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Registrar Trámite Presencial
        </a>
    </div>
</div>

<!-- Pestañas de Filtrado -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
        <ul class="nav nav-tabs card-header-tabs border-bottom-0">
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'pendientes' ? 'active fw-bold text-danger' : 'text-muted' ?>" href="<?= ViewHelper::url('/mesa-partes?tab=pendientes') ?>">
                    <i class="bi bi-inbox me-1"></i> Por Enviar a Dirección
                    <span class="badge bg-danger rounded-pill ms-1"><?= $counts['pendientes'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'enviados' ? 'active fw-bold text-primary' : 'text-muted' ?>" href="<?= ViewHelper::url('/mesa-partes?tab=enviados') ?>">
                    <i class="bi bi-send me-1"></i> Enviados a Dirección
                    <span class="badge bg-primary rounded-pill ms-1"><?= $counts['enviados'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'observados' ? 'active fw-bold text-warning-emphasis' : 'text-muted' ?>" href="<?= ViewHelper::url('/mesa-partes?tab=observados') ?>">
                    <i class="bi bi-exclamation-triangle me-1"></i> Observados
                    <span class="badge bg-warning text-dark rounded-pill ms-1"><?= $counts['observados'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'finalizados' ? 'active fw-bold text-success' : 'text-muted' ?>" href="<?= ViewHelper::url('/mesa-partes?tab=finalizados') ?>">
                    <i class="bi bi-check2-circle me-1"></i> Finalizados
                    <span class="badge bg-success rounded-pill ms-1"><?= $counts['finalizados'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'todos' ? 'active fw-bold text-dark' : 'text-muted' ?>" href="<?= ViewHelper::url('/mesa-partes?tab=todos') ?>">
                    <i class="bi bi-collection me-1"></i> Todos
                </a>
            </li>
        </ul>
    </div>

    <!-- Barra de Búsqueda Rápida -->
    <div class="card-body bg-light border-top border-bottom py-3">
        <form action="<?= ViewHelper::url('/mesa-partes') ?>" method="GET" class="row g-2">
            <input type="hidden" name="tab" value="<?= ViewHelper::escape($currentTab) ?>">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" name="q" value="<?= ViewHelper::escape($search) ?>" placeholder="Buscar por N° expediente, DNI, apellidos o código...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                <?php if (!empty($search)): ?>
                    <a href="<?= ViewHelper::url('/mesa-partes?tab=' . $currentTab) ?>" class="btn btn-outline-secondary">Limpiar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla de Expedientes -->
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
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expedientes)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary opacity-50"></i>
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
                            <td>
                                <?= ViewHelper::formatDateTime($exp['fecha_ingreso']) ?>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= ViewHelper::escape($exp['nombres'] . ' ' . $exp['apellido_paterno']) ?></div>
                                <div class="small text-muted">DNI: <?= ViewHelper::escape($exp['dni']) ?></div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 220px;" title="<?= ViewHelper::escape($exp['tramite_nombre'] ?? $exp['solicito']) ?>">
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
                                    <a href="<?= ViewHelper::url('/expedientes/' . $exp['id']) ?>" class="btn btn-outline-secondary" title="Ver Detalle Integral">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?= ViewHelper::url('/cargo/' . $exp['id']) ?>" target="_blank" class="btn btn-outline-secondary" title="Imprimir Cargo">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <?php if (in_array($exp['estado_codigo'], ['RECIBIDO', 'REGISTRADO'])): ?>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEnviarDir<?= $exp['id'] ?>" title="Remitir a Dirección General">
                                            <i class="bi bi-send-fill me-1"></i> Remitir
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Modal de Remisión a Dirección -->
                                <?php if (in_array($exp['estado_codigo'], ['RECIBIDO', 'REGISTRADO'])): ?>
                                    <div class="modal fade text-start" id="modalEnviarDir<?= $exp['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="<?= ViewHelper::url('/mesa-partes/' . $exp['id'] . '/enviar-direccion') ?>" method="POST">
                                                <?= Csrf::field() ?>
                                                <div class="modal-content">
                                                    <div class="modal-header bg-primary text-white" style="background-color: var(--color-primary) !important;">
                                                        <h6 class="modal-title fw-bold">
                                                            <i class="bi bi-send-fill me-2"></i> Remitir Expediente a Dirección General
                                                        </h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="p-3 bg-light rounded mb-3 small">
                                                            <div><strong>Expediente:</strong> <?= ViewHelper::escape($exp['numero_expediente']) ?></div>
                                                            <div><strong>Solicitante:</strong> <?= ViewHelper::escape($exp['nombres'] . ' ' . $exp['apellido_paterno']) ?></div>
                                                            <div><strong>Petición:</strong> <?= ViewHelper::escape($exp['tramite_nombre'] ?? $exp['solicito']) ?></div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="obs<?= $exp['id'] ?>" class="form-label fw-semibold small">Observación o Nota de Remisión:</label>
                                                            <textarea class="form-control" id="obs<?= $exp['id'] ?>" name="observacion" rows="3" placeholder="Indique alguna nota relevante para la Dirección General...">Se remite formalmente para conocimiento y derivación a la unidad orgánica respectiva.</textarea>
                                                        </div>

                                                        <div class="alert alert-info py-2 small mb-0">
                                                            <i class="bi bi-info-circle me-1"></i> El expediente cambiará al estado <strong>ENVIADO A DIRECCIÓN</strong> y quedará registrado de forma inmutable en la trazabilidad.
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                                            <i class="bi bi-check2-circle me-1"></i> Confirmar Remisión
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
