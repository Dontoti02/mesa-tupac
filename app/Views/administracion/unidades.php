<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Catálogo de Unidades Orgánicas</h3>
        <p class="text-muted mb-0">Estructura organizativa y dependencias del IESP Túpac Amaru para derivación de trámites.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevaUnidad">
            <i class="bi bi-plus-circle-fill me-1"></i> Registrar Nueva Unidad
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
        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-buildings me-2"></i>Unidades Registradas (<?= count($unidades) ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom">
            <thead>
                <tr>
                    <th style="width: 70px;">Orden</th>
                    <th>Código</th>
                    <th>Nombre de la Unidad</th>
                    <th>Responsable / Cargo</th>
                    <th>Contacto</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($unidades)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-buildings fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No hay unidades orgánicas registradas.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($unidades as $u): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $u['orden'] ?></td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border font-monospace px-2 py-1">
                                    <?= ViewHelper::escape($u['codigo']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= ViewHelper::escape($u['nombre']) ?></div>
                                <small class="text-muted text-truncate d-block" style="max-width: 320px;">
                                    <?= ViewHelper::escape($u['descripcion'] ?? 'Sin descripción') ?>
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold small"><?= ViewHelper::escape($u['responsable'] ?? 'Por asignar') ?></div>
                                <small class="text-muted"><?= ViewHelper::escape($u['cargo'] ?? '') ?></small>
                            </td>
                            <td>
                                <div class="small"><i class="bi bi-envelope me-1 text-muted"></i><?= ViewHelper::escape($u['correo'] ?? '-') ?></div>
                                <div class="small"><i class="bi bi-telephone me-1 text-muted"></i><?= ViewHelper::escape($u['telefono'] ?? '-') ?></div>
                            </td>
                            <td>
                                <?php if ((int)$u['estado'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-unidad"
                                    data-id="<?= $u['id'] ?>"
                                    data-codigo="<?= ViewHelper::escape($u['codigo']) ?>"
                                    data-nombre="<?= ViewHelper::escape($u['nombre']) ?>"
                                    data-descripcion="<?= ViewHelper::escape($u['descripcion'] ?? '') ?>"
                                    data-responsable="<?= ViewHelper::escape($u['responsable'] ?? '') ?>"
                                    data-cargo="<?= ViewHelper::escape($u['cargo'] ?? '') ?>"
                                    data-correo="<?= ViewHelper::escape($u['correo'] ?? '') ?>"
                                    data-telefono="<?= ViewHelper::escape($u['telefono'] ?? '') ?>"
                                    data-orden="<?= $u['orden'] ?>"
                                    data-estado="<?= $u['estado'] ?>">
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

<!-- Modal Nueva Unidad -->
<div class="modal fade" id="modalNuevaUnidad" tabindex="-1" aria-labelledby="modalNuevaUnidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary);">
                <h5 class="modal-title fw-bold" id="modalNuevaUnidadLabel">
                    <i class="bi bi-plus-circle-fill me-2"></i>Registrar Nueva Unidad Orgánica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= ViewHelper::url('/administracion/unidades') ?>" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código / Sigla <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="codigo" required placeholder="p. ej. UA, SA, DIR">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de la Unidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" required placeholder="p. ej. Unidad Académica">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" class="form-control" name="orden" value="10" min="0">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Descripción de Competencias</label>
                            <textarea class="form-control" name="descripcion" rows="2" placeholder="Funciones y competencia funcional de la unidad"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Responsable / Titular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="responsable" required placeholder="Nombre completo del titular">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo Institucional</label>
                            <input type="text" class="form-control" name="cargo" placeholder="p. ej. Jefe de Unidad Académica">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Institucional</label>
                            <input type="email" class="form-control" name="correo" placeholder="unidad@institucion.edu.pe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / Anexo</label>
                            <input type="text" class="form-control" name="telefono" placeholder="Anexo o celular">
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Unidad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Unidad -->
<div class="modal fade" id="modalEditarUnidad" tabindex="-1" aria-labelledby="modalEditarUnidadLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary-dark);">
                <h5 class="modal-title fw-bold" id="modalEditarUnidadLabel">
                    <i class="bi bi-pencil-square me-2"></i>Modificar Unidad Orgánica
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarUnidad" action="" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Código / Sigla <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="codigo" id="edit_unidad_codigo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre de la Unidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="edit_unidad_nombre" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" class="form-control" name="orden" id="edit_unidad_orden" min="0">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Descripción de Competencias</label>
                            <textarea class="form-control" name="descripcion" id="edit_unidad_descripcion" rows="2"></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Responsable / Titular <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="responsable" id="edit_unidad_responsable" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo Institucional</label>
                            <input type="text" class="form-control" name="cargo" id="edit_unidad_cargo">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Institucional</label>
                            <input type="email" class="form-control" name="correo" id="edit_unidad_correo">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Teléfono / Anexo</label>
                            <input type="text" class="form-control" name="telefono" id="edit_unidad_telefono">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Estado</label>
                            <select class="form-select" name="estado" id="edit_unidad_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Actualizar Unidad</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('modalEditarUnidad'));
    const form = document.getElementById('formEditarUnidad');

    document.querySelectorAll('.btn-editar-unidad').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action = '<?= ViewHelper::url('/administracion/unidades/') ?>' + id;
            document.getElementById('edit_unidad_codigo').value = this.dataset.codigo || '';
            document.getElementById('edit_unidad_nombre').value = this.dataset.nombre || '';
            document.getElementById('edit_unidad_descripcion').value = this.dataset.descripcion || '';
            document.getElementById('edit_unidad_responsable').value = this.dataset.responsable || '';
            document.getElementById('edit_unidad_cargo').value = this.dataset.cargo || '';
            document.getElementById('edit_unidad_correo').value = this.dataset.correo || '';
            document.getElementById('edit_unidad_telefono').value = this.dataset.telefono || '';
            document.getElementById('edit_unidad_orden').value = this.dataset.orden || '0';
            document.getElementById('edit_unidad_estado').value = this.dataset.estado || '1';
            editModal.show();
        });
    });
});
</script>
