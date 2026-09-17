<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Programas de Estudio Institucionales</h3>
        <p class="text-muted mb-0">Carreras profesionales y programas académicos del IESP Túpac Amaru para filiación de estudiantes en el FUT.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoPrograma">
            <i class="bi bi-mortarboard-fill me-1"></i> Registrar Nuevo Programa
        </button>
    </div>
</div>

<?php if ($flash = ViewHelper::flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= ViewHelper::escape($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<?php if ($flashError = ViewHelper::flash('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i><?= ViewHelper::escape($flashError) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-mortarboard me-2"></i>Programas Registrados (<?= count($programas) ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom">
            <thead>
                <tr>
                    <th style="width: 80px;">Orden</th>
                    <th>Código</th>
                    <th>Nombre del Programa de Estudio</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($programas)): ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-mortarboard fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No hay programas de estudio registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($programas as $p): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $p['orden'] ?></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border font-monospace px-2 py-1">
                                    <?= ViewHelper::escape($p['codigo']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= ViewHelper::escape($p['nombre']) ?></div>
                            </td>
                            <td>
                                <?php if ((int)$p['estado'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-prog"
                                    data-id="<?= $p['id'] ?>"
                                    data-codigo="<?= ViewHelper::escape($p['codigo']) ?>"
                                    data-nombre="<?= ViewHelper::escape($p['nombre']) ?>"
                                    data-orden="<?= $p['orden'] ?>"
                                    data-estado="<?= $p['estado'] ?>">
                                    <i class="bi bi-pencil-square me-1"></i> Editar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Nuevo Programa -->
<div class="modal fade" id="modalNuevoPrograma" tabindex="-1" aria-labelledby="modalNuevoProgramaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary);">
                <h5 class="modal-title fw-bold" id="modalNuevoProgramaLabel">
                    <i class="bi bi-mortarboard-fill me-2"></i>Registrar Nuevo Programa de Estudio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= ViewHelper::url('/administracion/programas') ?>" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="codigo" required placeholder="p. ej. DSI">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Orden de Visualización</label>
                            <input type="number" class="form-control" name="orden" value="10" min="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nombre del Programa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" required placeholder="p. ej. Desarrollo de Sistemas de Información">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Programa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Programa -->
<div class="modal fade" id="modalEditarPrograma" tabindex="-1" aria-labelledby="modalEditarProgramaLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary-dark);">
                <h5 class="modal-title fw-bold" id="modalEditarProgramaLabel">
                    <i class="bi bi-pencil-square me-2"></i>Modificar Programa de Estudio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarPrograma" action="" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="codigo" id="edit_prog_codigo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" class="form-control" name="orden" id="edit_prog_orden" min="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nombre del Programa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="edit_prog_nombre" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Estado</label>
                            <select class="form-select" name="estado" id="edit_prog_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Actualizar Programa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('modalEditarPrograma'));
    const form = document.getElementById('formEditarPrograma');

    document.querySelectorAll('.btn-editar-prog').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action = '<?= ViewHelper::url('/administracion/programas/') ?>' + id;
            document.getElementById('edit_prog_codigo').value = this.dataset.codigo || '';
            document.getElementById('edit_prog_nombre').value = this.dataset.nombre || '';
            document.getElementById('edit_prog_orden').value = this.dataset.orden || '0';
            document.getElementById('edit_prog_estado').value = this.dataset.estado || '1';
            editModal.show();
        });
    });
});
</script>
