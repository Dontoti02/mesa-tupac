<?php
use App\Helpers\ViewHelper;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Registro Maestro de Expedientes</h3>
        <p class="text-muted mb-0">Listado general con búsqueda avanzada, trazabilidad integral y filtros institucionales.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#panelFiltros" aria-expanded="false">
            <i class="bi bi-funnel me-1"></i> Filtros Avanzados
        </button>
        <a href="<?= ViewHelper::url('/mesa-partes/registrar') ?>" class="btn btn-primary fw-bold">
            <i class="bi bi-plus-circle-fill me-1"></i> Nuevo Trámite
        </a>
    </div>
</div>

<!-- Panel de Filtros Avanzados (Sección 17) -->
<div class="collapse <?= (!empty($filters['estado_id']) || !empty($filters['unidad_id']) || !empty($filters['prioridad_id']) || !empty($filters['programa_id']) || !empty($filters['fecha_desde'])) ? 'show' : '' ?> mb-4" id="panelFiltros">
    <div class="card shadow-sm border-0 bg-white">
        <div class="card-body p-4">
            <form action="<?= ViewHelper::url('/expedientes') ?>" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="q" class="form-label fw-semibold small">Búsqueda General:</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" class="form-control" id="q" name="q" value="<?= ViewHelper::escape($filters['q']) ?>" placeholder="N° expediente, código, DNI, apellidos...">
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label for="estado_id" class="form-label fw-semibold small">Estado del Trámite:</label>
                        <select class="form-select" id="estado_id" name="estado_id">
                            <option value="">-- Todos los Estados --</option>
                            <?php foreach ($estados as $es): ?>
                                <option value="<?= $es['id'] ?>" <?= $filters['estado_id'] == $es['id'] ? 'selected' : '' ?>>
                                    <?= ViewHelper::escape($es['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="unidad_id" class="form-label fw-semibold small">Ubicación / Unidad Actual:</label>
                        <select class="form-select" id="unidad_id" name="unidad_id">
                            <option value="">-- Todas las Unidades --</option>
                            <?php foreach ($unidades as $u): ?>
                                <option value="<?= $u['id'] ?>" <?= $filters['unidad_id'] == $u['id'] ? 'selected' : '' ?>>
                                    <?= ViewHelper::escape($u['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="prioridad_id" class="form-label fw-semibold small">Prioridad:</label>
                        <select class="form-select" id="prioridad_id" name="prioridad_id">
                            <option value="">-- Todas --</option>
                            <?php foreach ($prioridades as $p): ?>
                                <option value="<?= $p['id'] ?>" <?= $filters['prioridad_id'] == $p['id'] ? 'selected' : '' ?>>
                                    <?= ViewHelper::escape($p['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="programa_id" class="form-label fw-semibold small">Programa de Estudios:</label>
                        <select class="form-select" id="programa_id" name="programa_id">
                            <option value="">-- Todos los Programas --</option>
                            <?php foreach ($programas as $pr): ?>
                                <option value="<?= $pr['id'] ?>" <?= $filters['programa_id'] == $pr['id'] ? 'selected' : '' ?>>
                                    <?= ViewHelper::escape($pr['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="fecha_desde" class="form-label fw-semibold small">Fecha Desde:</label>
                        <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="<?= ViewHelper::escape($filters['fecha_desde']) ?>">
                    </div>

                    <div class="col-md-2">
                        <label for="fecha_hasta" class="form-label fw-semibold small">Fecha Hasta:</label>
                        <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="<?= ViewHelper::escape($filters['fecha_hasta']) ?>">
                    </div>

                    <div class="col-md-2">
                        <label for="per_page" class="form-label fw-semibold small">Mostrar Registros:</label>
                        <select class="form-select" id="per_page" name="per_page">
                            <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20 por pág.</option>
                            <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50 por pág.</option>
                            <option value="100" <?= $perPage == 100 ? 'selected' : '' ?>>100 por pág.</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="<?= ViewHelper::url('/expedientes') ?>" class="btn btn-outline-secondary btn-sm">Restablecer Filtros</a>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold px-4">
                            <i class="bi bi-funnel-fill me-1"></i> Aplicar Filtros
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tabla Maestra de Expedientes -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-folder-fill me-2" style="color: var(--color-primary);"></i>
            Total: <?= number_format($totalRecords) ?> expediente(s) encontrados
        </h6>
        <span class="small text-muted">Página <?= $currentPage ?> de <?= max(1, $totalPages) ?></span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom border-0">
            <thead>
                <tr>
                    <th>Expediente</th>
                    <th>Fecha</th>
                    <th>Solicitante</th>
                    <th>Petición (FUT)</th>
                    <th>Ubicación Actual</th>
                    <th>Estado</th>
                    <th>Prioridad</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($expedientes)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-1 d-block mb-2 text-secondary opacity-50"></i>
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
                                <div class="small text-muted font-monospace"><?= ViewHelper::escape($exp['codigo_seguimiento']) ?></div>
                            </td>
                            <td><?= ViewHelper::formatDateTime($exp['fecha_ingreso']) ?></td>
                            <td>
                                <div class="fw-semibold text-dark"><?= ViewHelper::escape($exp['nombres'] . ' ' . $exp['apellido_paterno']) ?></div>
                                <div class="small text-muted">DNI: <?= ViewHelper::escape($exp['dni']) ?></div>
                                <?php if (!empty($exp['programa_nombre'])): ?>
                                    <span class="badge bg-light text-muted border small" style="font-size: 0.68rem;"><?= ViewHelper::escape($exp['programa_nombre']) ?></span>
                                <?php endif; ?>
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
                                <?= ViewHelper::badgeEstado($exp['estado_nombre'] ?? 'Recibido', $exp['estado_color'] ?? '#6B7280', $exp['estado_icono'] ?? 'bi-clock') ?>
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
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación (Sección 41) -->
    <?php if ($totalPages > 1): ?>
        <div class="card-footer bg-white py-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="small text-muted">
                Mostrando <?= count($expedientes) ?> de <?= number_format($totalRecords) ?> registros
            </span>
            <nav aria-label="Navegación de páginas">
                <ul class="pagination pagination-sm mb-0">
                    <?php
                    $queryString = http_build_query(array_merge($filters, ['per_page' => $perPage]));
                    ?>
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= ViewHelper::url('/expedientes?' . $queryString . '&page=' . ($currentPage - 1)) ?>">Anterior</a>
                    </li>
                    <?php for ($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++): ?>
                        <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= ViewHelper::url('/expedientes?' . $queryString . '&page=' . $p) ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= ViewHelper::url('/expedientes?' . $queryString . '&page=' . ($currentPage + 1)) ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>
