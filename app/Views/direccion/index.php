<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Bandeja de Dirección General</h3>
        <p class="text-muted mb-0">Revisión directiva, derivación con instrucciones a unidades orgánicas y aprobación final.</p>
    </div>
</div>

<!-- Pestañas de la Dirección -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom-0 pb-0 pt-3">
        <ul class="nav nav-tabs card-header-tabs border-bottom-0">
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'por_derivar' ? 'active fw-bold text-danger' : 'text-muted' ?>" href="<?= ViewHelper::url('/direccion?tab=por_derivar') ?>">
                    <i class="bi bi-arrow-right-circle me-1"></i> Por Derivar
                    <span class="badge bg-danger rounded-pill ms-1"><?= $counts['por_derivar'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'derivados' ? 'active fw-bold text-primary' : 'text-muted' ?>" href="<?= ViewHelper::url('/direccion?tab=derivados') ?>">
                    <i class="bi bi-hourglass-split me-1"></i> Derivados en Trámite
                    <span class="badge bg-primary rounded-pill ms-1"><?= $counts['derivados'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'respuestas' ? 'active fw-bold text-success' : 'text-muted' ?>" href="<?= ViewHelper::url('/direccion?tab=respuestas') ?>">
                    <i class="bi bi-envelope-check-fill me-1"></i> Respuestas de Unidades
                    <span class="badge bg-success rounded-pill ms-1"><?= $counts['respuestas'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'observados' ? 'active fw-bold text-warning-emphasis' : 'text-muted' ?>" href="<?= ViewHelper::url('/direccion?tab=observados') ?>">
                    <i class="bi bi-exclamation-triangle me-1"></i> Observados
                    <span class="badge bg-warning text-dark rounded-pill ms-1"><?= $counts['observados'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'finalizados' ? 'active fw-bold text-secondary' : 'text-muted' ?>" href="<?= ViewHelper::url('/direccion?tab=finalizados') ?>">
                    <i class="bi bi-check2-all me-1"></i> Finalizados
                    <span class="badge bg-secondary rounded-pill ms-1"><?= $counts['finalizados'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= $currentTab === 'todos' ? 'active fw-bold text-dark' : 'text-muted' ?>" href="<?= ViewHelper::url('/direccion?tab=todos') ?>">
                    <i class="bi bi-collection me-1"></i> Todos
                </a>
            </li>
        </ul>
    </div>

    <!-- Filtros de Búsqueda -->
    <div class="card-body bg-light border-top border-bottom py-3">
        <form action="<?= ViewHelper::url('/direccion') ?>" method="GET" class="row g-2">
            <input type="hidden" name="tab" value="<?= ViewHelper::escape($currentTab) ?>">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control" name="q" value="<?= ViewHelper::escape($search) ?>" placeholder="Buscar por expediente, DNI, apellidos...">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-secondary">Filtrar</button>
                <?php if (!empty($search)): ?>
                    <a href="<?= ViewHelper::url('/direccion?tab=' . $currentTab) ?>" class="btn btn-outline-secondary">Limpiar</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Tabla de Expedientes en Dirección -->
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom border-0">
            <thead>
                <tr>
                    <th>N° Expediente</th>
                    <th>Fecha Ingreso</th>
                    <th>Solicitante</th>
                    <th>Procedimiento / Petición</th>
                    <th>Ubicación Actual</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th class="text-end">Acciones Directivas</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expedientes)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-folder-check fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No hay expedientes en esta bandeja de Dirección General.
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
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-building me-1 text-muted"></i><?= ViewHelper::escape($exp['unidad_nombre'] ?? 'Sin asignar') ?>
                                </span>
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

                                    <!-- Acciones según estado -->
                                    <?php if (in_array($exp['estado_codigo'], ['ENVIADO_A_DIRECCION', 'EN_REVISION'])): ?>
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalDerivar<?= $exp['id'] ?>" title="Derivar a Unidad Orgánica">
                                            <i class="bi bi-arrow-right-circle-fill me-1"></i> Derivar
                                        </button>
                                        <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalObservar<?= $exp['id'] ?>" title="Observar Expediente">
                                            <i class="bi bi-exclamation-triangle"></i>
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($exp['estado_codigo'] === 'RESPONDIDO'): ?>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAprobar<?= $exp['id'] ?>" title="Aprobar Respuesta y Finalizar">
                                            <i class="bi bi-check2-circle me-1"></i> Aprobar
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <!-- Modal Derivar -->
                                <div class="modal fade text-start" id="modalDerivar<?= $exp['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <form action="<?= ViewHelper::url('/direccion/' . $exp['id'] . '/derivar') ?>" method="POST">
                                            <?= Csrf::field() ?>
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white" style="background-color: var(--color-primary) !important;">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-diagram-3-fill me-2"></i> Derivar Expediente a Unidad Orgánica
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="p-3 bg-light rounded mb-3 small">
                                                        <div class="row">
                                                            <div class="col-md-4"><strong>Expediente:</strong> <?= ViewHelper::escape($exp['numero_expediente']) ?></div>
                                                            <div class="col-md-8"><strong>Solicitante:</strong> <?= ViewHelper::escape($exp['nombres'] . ' ' . $exp['apellido_paterno']) ?> (DNI: <?= ViewHelper::escape($exp['dni']) ?>)</div>
                                                            <div class="col-12 mt-1"><strong>Petición:</strong> <?= ViewHelper::escape($exp['tramite_nombre'] ?? $exp['solicito']) ?></div>
                                                        </div>
                                                    </div>

                                                    <div class="row g-3">
                                                        <div class="col-md-8">
                                                            <label for="ud<?= $exp['id'] ?>" class="form-label fw-semibold small">Unidad Orgánica Destino: <span class="text-danger">*</span></label>
                                                            <select class="form-select" id="ud<?= $exp['id'] ?>" name="unidad_destino_id" required>
                                                                <option value="">-- Seleccione Unidad Responsable --</option>
                                                                <?php foreach ($unidades as $u): ?>
                                                                    <?php if ($u['id'] != 1): // no auto-derivar a Direccion ?>
                                                                        <option value="<?= $u['id'] ?>">
                                                                            <?= ViewHelper::escape($u['codigo']) ?> - <?= ViewHelper::escape($u['nombre']) ?> (<?= ViewHelper::escape($u['responsable'] ?? 'Responsable') ?>)
                                                                        </option>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label for="pr<?= $exp['id'] ?>" class="form-label fw-semibold small">Prioridad de Atención:</label>
                                                            <select class="form-select" id="pr<?= $exp['id'] ?>" name="prioridad_id">
                                                                <?php foreach ($prioridades as $p): ?>
                                                                    <option value="<?= $p['id'] ?>" <?= $p['id'] == $exp['prioridad_id'] ? 'selected' : '' ?>>
                                                                        <?= ViewHelper::escape($p['nombre']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <div class="col-12">
                                                            <label for="ins<?= $exp['id'] ?>" class="form-label fw-semibold small">Instrucción Directiva / Proveído de Atención: <span class="text-danger">*</span></label>
                                                            <textarea class="form-control" id="ins<?= $exp['id'] ?>" name="instruccion" rows="3" required placeholder="Escriba el proveído o instrucciones directivas para el responsable de la unidad...">Pase a la Unidad Orgánica respectiva para su debida atención, informe técnico y emisión del proyecto de respuesta dentro del plazo de ley.</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary btn-sm fw-bold">
                                                        <i class="bi bi-send-check me-1"></i> Derivar Expediente
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Observar -->
                                <div class="modal fade text-start" id="modalObservar<?= $exp['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <form action="<?= ViewHelper::url('/direccion/' . $exp['id'] . '/observar') ?>" method="POST">
                                            <?= Csrf::field() ?>
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-exclamation-triangle-fill me-2"></i> Emitir Observación Formal
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="mobs<?= $exp['id'] ?>" class="form-label fw-semibold small">Fundamento de la Observación (Se notificará al administrado): <span class="text-danger">*</span></label>
                                                        <textarea class="form-control" id="mobs<?= $exp['id'] ?>" name="observacion" rows="4" required placeholder="Detalle con claridad los requisitos faltantes o causales de observación que impiden continuar con el trámite..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-danger btn-sm fw-bold">Registrar Observación</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Modal Aprobar y Finalizar -->
                                <?php if ($exp['estado_codigo'] === 'RESPONDIDO'): ?>
                                    <div class="modal fade text-start" id="modalAprobar<?= $exp['id'] ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form action="<?= ViewHelper::url('/direccion/' . $exp['id'] . '/aprobar') ?>" method="POST">
                                                <?= Csrf::field() ?>
                                                <div class="modal-content">
                                                    <div class="modal-header bg-success text-white">
                                                        <h6 class="modal-title fw-bold">
                                                            <i class="bi bi-check2-all me-2"></i> Aprobar Respuesta y Finalizar Trámite
                                                        </h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="alert alert-success py-2 small mb-3">
                                                            <i class="bi bi-info-circle me-1"></i> La unidad responsable ya emitió su respuesta. Al aprobar este trámite, el expediente pasará al estado <strong>FINALIZADO</strong> y se notificará a Mesa de Partes para la entrega oficial.
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="aobs<?= $exp['id'] ?>" class="form-label fw-semibold small">Resolución o Disposición Directiva:</label>
                                                            <textarea class="form-control" id="aobs<?= $exp['id'] ?>" name="observacion" rows="3">Aprobado y emitido con la conformidad de la Dirección General. Proceder con la entrega al administrado.</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer bg-light">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                                                        <button type="submit" class="btn btn-success btn-sm fw-bold">
                                                            <i class="bi bi-check-circle-fill me-1"></i> Aprobar y Concluir Trámite
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
