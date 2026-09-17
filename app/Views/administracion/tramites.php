<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Catálogo de Trámites del FUT</h3>
        <p class="text-muted mb-0">Procedimientos institucionales, plazos normativos, requisitos documentarios y costos.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoTramite">
            <i class="bi bi-plus-circle-fill me-1"></i> Registrar Nuevo Trámite
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
        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-card-checklist me-2"></i>Procedimientos Disponibles (<?= count($tramites) ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Categoría</th>
                    <th>Nombre del Trámite</th>
                    <th>Unidad Destino Sugerida</th>
                    <th>Plazo</th>
                    <th>Costo</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tramites)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-card-checklist fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No hay trámites registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($tramites as $t): ?>
                        <tr>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border font-monospace px-2 py-1">
                                    <?= ViewHelper::escape($t['codigo']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <?= ViewHelper::escape($t['categoria_nombre'] ?? 'General') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-bold text-dark"><?= ViewHelper::escape($t['nombre']) ?></div>
                                <?php if (!empty($t['requisitos'])): ?>
                                    <small class="text-muted text-truncate d-block" style="max-width: 320px;">
                                        <i class="bi bi-check-circle me-1"></i><?= ViewHelper::escape($t['requisitos']) ?>
                                    </small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="fw-semibold text-secondary">
                                    <?= ViewHelper::escape($t['unidad_sugerida_nombre'] ?? 'A definir por Dirección') ?>
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-light text-primary border">
                                    <i class="bi bi-clock-history me-1"></i><?= (int)$t['plazo_dias'] ?> días
                                </span>
                            </td>
                            <td>
                                <?php if ((float)$t['costo'] > 0): ?>
                                    <span class="fw-bold text-success">S/ <?= number_format((float)$t['costo'], 2) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Gratuito</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ((int)$t['estado'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-tramite"
                                    data-id="<?= $t['id'] ?>"
                                    data-categoria-id="<?= $t['categoria_id'] ?>"
                                    data-codigo="<?= ViewHelper::escape($t['codigo']) ?>"
                                    data-nombre="<?= ViewHelper::escape($t['nombre']) ?>"
                                    data-descripcion="<?= ViewHelper::escape($t['descripcion'] ?? '') ?>"
                                    data-unidad-sugerida-id="<?= $t['unidad_sugerida_id'] ?? '' ?>"
                                    data-requisitos="<?= ViewHelper::escape($t['requisitos'] ?? '') ?>"
                                    data-plazo-dias="<?= $t['plazo_dias'] ?>"
                                    data-costo="<?= $t['costo'] ?>"
                                    data-requiere-pago="<?= $t['requiere_pago'] ?>"
                                    data-admite-virtual="<?= $t['admite_virtual'] ?>"
                                    data-orden="<?= $t['orden'] ?>"
                                    data-estado="<?= $t['estado'] ?>">
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

<!-- Modal Nuevo Trámite -->
<div class="modal fade" id="modalNuevoTramite" tabindex="-1" aria-labelledby="modalNuevoTramiteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary);">
                <h5 class="modal-title fw-bold" id="modalNuevoTramiteLabel">
                    <i class="bi bi-plus-circle-fill me-2"></i>Registrar Nuevo Trámite del FUT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= ViewHelper::url('/administracion/tramites') ?>" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría del Trámite <span class="text-danger">*</span></label>
                            <select class="form-select" name="categoria_id" required>
                                <option value="">-- Seleccionar Categoría --</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= ViewHelper::escape($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="codigo" required placeholder="p. ej. CERT-01">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Plazo (Días hábiles) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="plazo_dias" value="15" min="1" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nombre del Trámite <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" required placeholder="p. ej. Certificado Modular Oficial">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Unidad Orgánica Sugerida de Atención</label>
                            <select class="form-select" name="unidad_sugerida_id">
                                <option value="">-- Sugerencia Automática / Sin asignar --</option>
                                <?php foreach ($unidades as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= ViewHelper::escape($u['nombre']) ?> (<?= ViewHelper::escape($u['codigo']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Requisitos Documentarios</label>
                            <textarea class="form-control" name="requisitos" rows="3" placeholder="Requisitos obligatorios que debe adjuntar el solicitante"></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Costo Institucional (S/)</label>
                            <input type="number" step="0.01" class="form-control" name="costo" value="0.00" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" class="form-control" name="orden" value="10" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold d-block">Opciones</label>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="checkbox" name="requiere_pago" value="1" id="nuevo_req_pago">
                                <label class="form-check-label" for="nuevo_req_pago">Requiere Pago</label>
                            </div>
                            <div class="form-check form-check-inline mt-2">
                                <input class="form-check-input" type="checkbox" name="admite_virtual" value="1" id="nuevo_adm_virt" checked>
                                <label class="form-check-label" for="nuevo_adm_virt">Admite Virtual</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Trámite</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Trámite -->
<div class="modal fade" id="modalEditarTramite" tabindex="-1" aria-labelledby="modalEditarTramiteLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary-dark);">
                <h5 class="modal-title fw-bold" id="modalEditarTramiteLabel">
                    <i class="bi bi-pencil-square me-2"></i>Modificar Trámite del FUT
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarTramite" action="" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Categoría <span class="text-danger">*</span></label>
                            <select class="form-select" name="categoria_id" id="edit_tram_cat" required>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= ViewHelper::escape($cat['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="codigo" id="edit_tram_codigo" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Plazo (Días) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="plazo_dias" id="edit_tram_plazo" required min="1">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nombre del Trámite <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nombre" id="edit_tram_nombre" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Unidad Orgánica Sugerida</label>
                            <select class="form-select" name="unidad_sugerida_id" id="edit_tram_unidad">
                                <option value="">-- Sin sugerencia --</option>
                                <?php foreach ($unidades as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= ViewHelper::escape($u['nombre']) ?> (<?= ViewHelper::escape($u['codigo']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Requisitos Documentarios</label>
                            <textarea class="form-control" name="requisitos" id="edit_tram_requisitos" rows="3"></textarea>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Costo (S/)</label>
                            <input type="number" step="0.01" class="form-control" name="costo" id="edit_tram_costo" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Orden</label>
                            <input type="number" class="form-control" name="orden" id="edit_tram_orden" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estado</label>
                            <select class="form-select" name="estado" id="edit_tram_estado">
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="requiere_pago" value="1" id="edit_tram_requiere_pago">
                                <label class="form-check-label" for="edit_tram_requiere_pago">Requiere Pago Previo</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="admite_virtual" value="1" id="edit_tram_admite_virtual">
                                <label class="form-check-label" for="edit_tram_admite_virtual">Admite Ingreso Virtual</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Actualizar Trámite</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('modalEditarTramite'));
    const form = document.getElementById('formEditarTramite');

    document.querySelectorAll('.btn-editar-tramite').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action = '<?= ViewHelper::url('/administracion/tramites/') ?>' + id;
            document.getElementById('edit_tram_cat').value = this.dataset.categoriaId || '';
            document.getElementById('edit_tram_codigo').value = this.dataset.codigo || '';
            document.getElementById('edit_tram_nombre').value = this.dataset.nombre || '';
            document.getElementById('edit_tram_unidad').value = this.dataset.unidadSugeridaId || '';
            document.getElementById('edit_tram_requisitos').value = this.dataset.requisitos || '';
            document.getElementById('edit_tram_plazo').value = this.dataset.plazoDias || '15';
            document.getElementById('edit_tram_costo').value = this.dataset.costo || '0.00';
            document.getElementById('edit_tram_orden').value = this.dataset.orden || '0';
            document.getElementById('edit_tram_estado').value = this.dataset.estado || '1';
            document.getElementById('edit_tram_requiere_pago').checked = (this.dataset.requierePago === '1');
            document.getElementById('edit_tram_admite_virtual').checked = (this.dataset.admiteVirtual === '1');
            editModal.show();
        });
    });
});
</script>
