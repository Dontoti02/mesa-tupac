<?php
use App\Helpers\ViewHelper;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Auditoría y Trazabilidad de Seguridad</h3>
        <p class="text-muted mb-0">Registro inmutable de acciones de usuarios, inicios de sesión, cambios de estado e inspección forense.</p>
    </div>
</div>

<!-- Filtros de Auditoría -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3 bg-light">
        <form action="<?= ViewHelper::url('/auditoria') ?>" method="GET" class="row g-2">
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" name="fecha_desde" value="<?= ViewHelper::escape($filters['fecha_desde']) ?>">
            </div>
            <div class="col-md-2">
                <input type="date" class="form-control form-control-sm" name="fecha_hasta" value="<?= ViewHelper::escape($filters['fecha_hasta']) ?>">
            </div>
            <div class="col-md-3">
                <select class="form-select form-select-sm" name="usuario_id">
                    <option value="">-- Todos los Usuarios --</option>
                    <?php foreach ($usuarios as $u): ?>
                        <option value="<?= $u['id'] ?>" <?= $filters['usuario_id'] == $u['id'] ? 'selected' : '' ?>>
                            <?= ViewHelper::escape($u['nombres'] . ' ' . $u['apellidos']) ?> (<?= ViewHelper::escape($u['username']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control form-control-sm" name="accion" value="<?= ViewHelper::escape($filters['accion']) ?>" placeholder="Buscar por Acción (p. ej. LOGIN, DERIVAR)">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-sm btn-primary fw-bold flex-grow-1">Filtrar</button>
                <a href="<?= ViewHelper::url('/auditoria') ?>" class="btn btn-sm btn-outline-secondary">Limpiar</a>
            </div>
        </form>
    </div>
</div>

<!-- Listado de Auditoría -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-clock-history me-2"></i>Eventos Registrados (<?= count($logs) ?>)
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom" style="font-size: 0.85rem;">
            <thead>
                <tr>
                    <th>Fecha y Hora</th>
                    <th>Usuario / Funcionario</th>
                    <th>Acción Realizada</th>
                    <th>Módulo / Tabla</th>
                    <th>Dirección IP</th>
                    <th class="text-end">Detalle</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-shield-check fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No se encontraron registros de auditoría para estos filtros.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $l): ?>
                        <tr>
                            <td>
                                <div class="fw-semibold text-dark"><?= ViewHelper::formatDate($l['created_at'], true) ?></div>
                            </td>
                            <td>
                                <?php if (!empty($l['username'])): ?>
                                    <div class="fw-bold text-dark"><?= ViewHelper::escape($l['nombres'] . ' ' . $l['apellidos']) ?></div>
                                    <small class="text-muted font-monospace"><?= ViewHelper::escape($l['username']) ?> &bull; <?= ViewHelper::escape($l['cargo'] ?? '') ?></small>
                                <?php else: ?>
                                    <span class="text-muted fst-italic">Sistema / Anónimo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border font-monospace px-2 py-1">
                                    <?= ViewHelper::escape($l['accion']) ?>
                                </span>
                            </td>
                            <td>
                                <code><?= ViewHelper::escape($l['modulo']) ?></code>
                                <?php if (!empty($l['registro_id'])): ?>
                                    <span class="badge bg-light text-muted border">ID: <?= $l['registro_id'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="font-monospace text-muted"><?= ViewHelper::escape($l['ip']) ?></span>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-ver-diff" 
                                    data-id="<?= $l['id'] ?>"
                                    data-prev="<?= ViewHelper::escape($l['datos_anteriores'] ?? '') ?>"
                                    data-next="<?= ViewHelper::escape($l['datos_nuevos'] ?? '') ?>"
                                    data-ua="<?= ViewHelper::escape($l['user_agent'] ?? '') ?>">
                                    <i class="bi bi-search me-1"></i> Ver Datos
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Detalle Diff de Auditoría -->
<div class="modal fade" id="modalAuditoriaDiff" tabindex="-1" aria-labelledby="modalAuditoriaDiffLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="modalAuditoriaDiffLabel">
                    <i class="bi bi-code-square me-2 text-warning"></i>Inspección Técnica de Auditoría
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="fw-bold small text-muted text-uppercase">Navegador / Dispositivo (User Agent)</label>
                    <div id="diffUserAgent" class="p-2 bg-light border rounded small font-monospace text-break text-muted"></div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold small text-danger text-uppercase"><i class="bi bi-dash-circle me-1"></i> Estado Anterior (JSON)</label>
                        <pre id="diffPrev" class="p-3 bg-light border rounded small text-dark" style="max-height: 300px; overflow-y: auto;"></pre>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-success text-uppercase"><i class="bi bi-plus-circle me-1"></i> Estado Nuevo (JSON)</label>
                        <pre id="diffNext" class="p-3 bg-light border rounded small text-dark" style="max-height: 300px; overflow-y: auto;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modalDiff = new bootstrap.Modal(document.getElementById('modalAuditoriaDiff'));

    document.querySelectorAll('.btn-ver-diff').forEach(btn => {
        btn.addEventListener('click', function() {
            const ua = this.dataset.ua || 'No registrado';
            const prev = this.dataset.prev;
            const next = this.dataset.next;

            document.getElementById('diffUserAgent').textContent = ua;

            try {
                document.getElementById('diffPrev').textContent = prev ? JSON.stringify(JSON.parse(prev), null, 2) : 'Sin cambios anteriores / Registro inicial';
            } catch(e) {
                document.getElementById('diffPrev').textContent = prev || 'Sin datos';
            }

            try {
                document.getElementById('diffNext').textContent = next ? JSON.stringify(JSON.parse(next), null, 2) : 'Sin payload posterior';
            } catch(e) {
                document.getElementById('diffNext').textContent = next || 'Sin datos';
            }

            modalDiff.show();
        });
    });
});
</script>
