<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Estados Normativos de Expedientes</h3>
        <p class="text-muted mb-0">Ciclo de vida, colores de identificación, iconos y visibilidad ciudadana para la consulta pública.</p>
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
        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-tags me-2"></i>Estados Configurados (<?= count($estados) ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom">
            <thead>
                <tr>
                    <th style="width: 70px;">Orden</th>
                    <th>Código del Estado</th>
                    <th>Nombre y Etiqueta Visual</th>
                    <th>Icono</th>
                    <th>Visibilidad Ciudadana</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($estados)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-tags fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No hay estados configurados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($estados as $e): ?>
                        <tr>
                            <td class="text-center fw-bold text-muted"><?= $e['orden'] ?></td>
                            <td>
                                <code class="fw-bold fs-6"><?= ViewHelper::escape($e['codigo']) ?></code>
                            </td>
                            <td>
                                <span class="badge px-3 py-2 text-white fw-bold shadow-xs" style="background-color: <?= ViewHelper::escape($e['color']) ?>;">
                                    <i class="<?= ViewHelper::escape($e['icono']) ?> me-1"></i>
                                    <?= ViewHelper::escape($e['nombre']) ?>
                                </span>
                                <?php if (!empty($e['descripcion'])): ?>
                                    <small class="text-muted d-block mt-1"><?= ViewHelper::escape($e['descripcion']) ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fs-5" style="color: <?= ViewHelper::escape($e['color']) ?>;">
                                    <i class="<?= ViewHelper::escape($e['icono']) ?>"></i>
                                </span>
                                <small class="text-muted font-monospace ms-1"><?= ViewHelper::escape($e['icono']) ?></small>
                            </td>
                            <td>
                                <?php if ((int)$e['es_publico'] === 1): ?>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">
                                        <i class="bi bi-eye-fill me-1"></i> Visible al Ciudadano
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <i class="bi bi-eye-slash-fill me-1"></i> Uso Interno
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$e['activo'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-estado"
                                    data-id="<?= $e['id'] ?>"
                                    data-codigo="<?= ViewHelper::escape($e['codigo']) ?>"
                                    data-nombre="<?= ViewHelper::escape($e['nombre']) ?>"
                                    data-color="<?= ViewHelper::escape($e['color']) ?>"
                                    data-icono="<?= ViewHelper::escape($e['icono']) ?>"
                                    data-es-publico="<?= $e['es_publico'] ?>"
                                    data-activo="<?= $e['activo'] ?>">
                                    <i class="bi bi-pencil-square me-1"></i> Personalizar
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Editar Estado -->
<div class="modal fade" id="modalEditarEstado" tabindex="-1" aria-labelledby="modalEditarEstadoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary-dark);">
                <h5 class="modal-title fw-bold" id="modalEditarEstadoLabel">
                    <i class="bi bi-palette-fill me-2"></i>Personalizar Estado de Trámite
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarEstado" action="" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Código Normativo</label>
                            <input type="text" class="form-control bg-light font-monospace fw-bold" id="edit_estado_codigo" readonly>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nombre Visible <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="edit_estado_nombre" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Color Hexadecimal <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="picker_color" value="#B3261E">
                                <input type="text" class="form-control font-monospace" name="color" id="edit_estado_color" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Clase Icono Bootstrap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control font-monospace" name="icono" id="edit_estado_icono" required placeholder="bi bi-check-circle">
                        </div>

                        <div class="col-md-12 pt-2">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="es_publico" value="1" id="edit_estado_es_publico">
                                <label class="form-check-label fw-semibold" for="edit_estado_es_publico">
                                    Mostrar este estado en el portal de Consulta Ciudadana
                                </label>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="activo" value="1" id="edit_estado_activo">
                                <label class="form-check-label fw-semibold" for="edit_estado_activo">
                                    Estado Operativo Activo
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Actualizar Estado</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('modalEditarEstado'));
    const form = document.getElementById('formEditarEstado');
    const picker = document.getElementById('picker_color');
    const colorInput = document.getElementById('edit_estado_color');

    picker.addEventListener('input', function() {
        colorInput.value = this.value;
    });
    colorInput.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            picker.value = this.value;
        }
    });

    document.querySelectorAll('.btn-editar-estado').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action = '<?= ViewHelper::url('/administracion/estados/') ?>' + id;
            document.getElementById('edit_estado_codigo').value = this.dataset.codigo || '';
            document.getElementById('edit_estado_nombre').value = this.dataset.nombre || '';
            document.getElementById('edit_estado_color').value = this.dataset.color || '#374151';
            picker.value = this.dataset.color || '#374151';
            document.getElementById('edit_estado_icono').value = this.dataset.icono || 'bi bi-tag';
            document.getElementById('edit_estado_es_publico').checked = (this.dataset.esPublico === '1');
            document.getElementById('edit_estado_activo').checked = (this.dataset.activo === '1');
            editModal.show();
        });
    });
});
</script>
