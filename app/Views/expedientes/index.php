<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;

$isSuperadmin = in_array('superadmin', is_array($currentUser) ? ($currentUser['roles'] ?? []) : [], true);
$colspan = $isSuperadmin ? 9 : 8;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Registro Maestro de Expedientes</h3>
        <p class="text-muted mb-0">Listado general con búsqueda avanzada, trazabilidad integral y filtros institucionales.</p>
    </div>
    <div class="d-flex gap-2">
        <?php if ($isSuperadmin): ?>
            <button type="button" id="btnEliminarSeleccionados" class="btn btn-outline-danger" disabled
                    data-bs-toggle="modal" data-bs-target="#modalEliminarExpedientes">
                <i class="bi bi-trash3 me-1"></i> Eliminar seleccionados
                <span class="badge bg-danger ms-1" id="contadorSeleccionados">0</span>
            </button>
        <?php endif; ?>
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

    <?php if ($isSuperadmin): ?>
    <form id="formExpedientes" action="<?= ViewHelper::url('/expedientes/eliminar') ?>" method="POST">
        <?= Csrf::field() ?>
    <?php endif; ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom border-0">
            <thead>
                <tr>
                    <?php if ($isSuperadmin): ?>
                        <th style="width: 38px;">
                            <input type="checkbox" class="form-check-input" id="chkSeleccionarTodos"
                                   title="Seleccionar todos" aria-label="Seleccionar todos">
                        </th>
                    <?php endif; ?>
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
                        <td colspan="<?= $colspan ?>" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron expedientes con los criterios seleccionados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($expedientes as $exp): ?>
                        <tr>
                            <?php if ($isSuperadmin): ?>
                                <td>
                                    <input type="checkbox" class="form-check-input chk-expediente"
                                           name="expediente_ids[]" value="<?= (int)$exp['id'] ?>"
                                           aria-label="Seleccionar expediente <?= ViewHelper::escape($exp['numero_expediente']) ?>">
                                </td>
                            <?php endif; ?>
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
    <?php if ($isSuperadmin): ?>
    </form>
    <?php endif; ?>

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

<?php if ($isSuperadmin): ?>
<!-- Modal de confirmación de eliminación -->
<div class="modal fade" id="modalEliminarExpedientes" tabindex="-1" aria-labelledby="modalEliminarExpedientesLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary);">
                <h5 class="modal-title fw-bold" id="modalEliminarExpedientesLabel">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirmar eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-2">¿Está seguro de eliminar <strong id="modalContadorSeleccionados">0</strong> expediente(s) del Registro Maestro?</p>
                <p class="text-muted small mb-0">Dejarán de mostrarse en el sistema, pero la información se conserva para fines de trazabilidad y auditoría.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger fw-bold" id="btnConfirmarEliminar">
                    <i class="bi bi-trash3 me-1"></i> Eliminar
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var form = document.getElementById('formExpedientes');
    if (!form) { return; }

    var selectAll = document.getElementById('chkSeleccionarTodos');
    var btnEliminar = document.getElementById('btnEliminarSeleccionados');
    var contador = document.getElementById('contadorSeleccionados');
    var modalContador = document.getElementById('modalContadorSeleccionados');
    var btnConfirmar = document.getElementById('btnConfirmarEliminar');
    var checkboxes = function () {
        return Array.prototype.slice.call(form.querySelectorAll('.chk-expediente'));
    };
    var seleccionados = function () {
        return checkboxes().filter(function (c) { return c.checked; });
    };

    function actualizar() {
        var total = checkboxes().length;
        var marcados = seleccionados().length;
        if (contador) { contador.textContent = marcados; }
        if (btnEliminar) { btnEliminar.disabled = marcados === 0; }
        if (selectAll) {
            selectAll.checked = total > 0 && marcados === total;
            selectAll.indeterminate = marcados > 0 && marcados < total;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes().forEach(function (c) { c.checked = selectAll.checked; });
            actualizar();
        });
    }

    checkboxes().forEach(function (c) {
        c.addEventListener('change', actualizar);
    });

    var modal = document.getElementById('modalEliminarExpedientes');
    if (modal) {
        modal.addEventListener('show.bs.modal', function () {
            if (modalContador) { modalContador.textContent = seleccionados().length; }
        });
    }

    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', function () {
            if (seleccionados().length > 0) { form.submit(); }
        });
    }

    actualizar();
})();
</script>
<?php endif; ?>
