<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Administración de Usuarios y Funcionarios</h3>
        <p class="text-muted mb-0">Gestión de cuentas institucionales, asignación de roles y unidades orgánicas.</p>
    </div>
    <div>
        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalNuevoUsuario">
            <i class="bi bi-person-plus-fill me-1"></i> Registrar Nuevo Usuario
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
        <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-people me-2"></i>Usuarios Registrados (<?= count($usuarios) ?>)</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 table-custom">
            <thead>
                <tr>
                    <th>DNI / Usuario</th>
                    <th>Nombres y Apellidos</th>
                    <th>Cargo / Unidad</th>
                    <th>Rol Asignado</th>
                    <th>Estado</th>
                    <th>Último Acceso</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No hay usuarios registrados.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark"><?= ViewHelper::escape($u['username']) ?></div>
                                <span class="badge bg-light text-secondary border font-monospace"><?= ViewHelper::escape($u['dni']) ?></span>
                            </td>
                            <td>
                                <div class="fw-semibold"><?= ViewHelper::escape($u['nombres'] . ' ' . $u['apellidos']) ?></div>
                                <small class="text-muted"><i class="bi bi-envelope me-1"></i><?= ViewHelper::escape($u['email']) ?></small>
                            </td>
                            <td>
                                <div class="small fw-semibold"><?= ViewHelper::escape($u['cargo'] ?? 'Sin cargo') ?></div>
                                <small class="text-muted"><?= ViewHelper::escape($u['unidad_nombre'] ?? 'Sin unidad asignada') ?></small>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                    <i class="bi bi-shield-check me-1"></i><?= ViewHelper::escape($u['rol_nombre'] ?? 'Sin Rol') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ((int)$u['estado'] === 1): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Activo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= !empty($u['ultimo_login']) ? ViewHelper::formatDate($u['ultimo_login'], true) : 'Nunca' ?>
                                </small>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary btn-editar-usuario"
                                    data-id="<?= $u['id'] ?>"
                                    data-dni="<?= ViewHelper::escape($u['dni']) ?>"
                                    data-nombres="<?= ViewHelper::escape($u['nombres']) ?>"
                                    data-apellidos="<?= ViewHelper::escape($u['apellidos']) ?>"
                                    data-username="<?= ViewHelper::escape($u['username']) ?>"
                                    data-email="<?= ViewHelper::escape($u['email']) ?>"
                                    data-telefono="<?= ViewHelper::escape($u['telefono'] ?? '') ?>"
                                    data-cargo="<?= ViewHelper::escape($u['cargo'] ?? '') ?>"
                                    data-unidad-id="<?= $u['unidad_id'] ?? '' ?>"
                                    data-rol-id="<?= $u['rol_id'] ?? '' ?>"
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

<!-- Modal Registrar Usuario -->
<div class="modal fade" id="modalNuevoUsuario" tabindex="-1" aria-labelledby="modalNuevoUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary);">
                <h5 class="modal-title fw-bold" id="modalNuevoUsuarioLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Usuario / Funcionario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= ViewHelper::url('/administracion/usuarios') ?>" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">DNI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="dni" maxlength="8" required placeholder="8 dígitos">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nombre de Usuario <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" required placeholder="p. ej. jperez">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Contraseña Inicial <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" minlength="8" required placeholder="Mínimo 8 caracteres">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="nombres" required placeholder="NOMBRES">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="apellidos" required placeholder="APELLIDOS">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" required placeholder="correo@institucion.edu.pe">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <input type="text" class="form-control" name="telefono" placeholder="987654321">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo Institucional <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="cargo" required placeholder="p. ej. Jefe de Unidad / Especialista">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol del Sistema <span class="text-danger">*</span></label>
                            <select class="form-select" name="rol_id" required>
                                <option value="">-- Seleccione Rol --</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= ViewHelper::escape($r['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Unidad Orgánica Asignada</label>
                            <select class="form-select" name="unidad_id">
                                <option value="">-- Ninguna / Ámbito Institucional Global --</option>
                                <?php foreach ($unidades as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= ViewHelper::escape($u['nombre']) ?> (<?= ViewHelper::escape($u['codigo']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Si el usuario pertenece a una jefatura o unidad específica, selecciónela aquí.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Usuario -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-labelledby="modalEditarUsuarioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header text-white" style="background: var(--color-primary-dark);">
                <h5 class="modal-title fw-bold" id="modalEditarUsuarioLabel">
                    <i class="bi bi-pencil-square me-2"></i>Modificar Datos de Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarUsuario" action="" method="POST">
                <?= Csrf::field() ?>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">DNI <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="dni" id="edit_dni" maxlength="8" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nombre de Usuario</label>
                            <input type="text" class="form-control bg-light" name="username" id="edit_username" readonly>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Nueva Contraseña <small class="text-muted">(Opcional)</small></label>
                            <input type="password" class="form-control" name="password" minlength="8" placeholder="Dejar en blanco para no cambiar">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombres <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="nombres" id="edit_nombres" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Apellidos <span class="text-danger">*</span></label>
                            <input type="text" class="form-control text-uppercase" name="apellidos" id="edit_apellidos" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" id="edit_email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Teléfono / Celular</label>
                            <input type="text" class="form-control" name="telefono" id="edit_telefono">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cargo Institucional <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="cargo" id="edit_cargo" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Rol del Sistema <span class="text-danger">*</span></label>
                            <select class="form-select" name="rol_id" id="edit_rol_id" required>
                                <option value="">-- Seleccione Rol --</option>
                                <?php foreach ($roles as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= ViewHelper::escape($r['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Unidad Orgánica Asignada</label>
                            <select class="form-select" name="unidad_id" id="edit_unidad_id">
                                <option value="">-- Ninguna / Ámbito Institucional Global --</option>
                                <?php foreach ($unidades as $u): ?>
                                    <option value="<?= $u['id'] ?>"><?= ViewHelper::escape($u['nombre']) ?> (<?= ViewHelper::escape($u['codigo']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Estado de la Cuenta</label>
                            <select class="form-select" name="estado" id="edit_estado">
                                <option value="1">Activo / Operativo</option>
                                <option value="0">Inactivo / Bloqueado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Actualizar Datos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    const form = document.getElementById('formEditarUsuario');

    document.querySelectorAll('.btn-editar-usuario').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action = '<?= ViewHelper::url('/administracion/usuarios/') ?>' + id;
            document.getElementById('edit_dni').value = this.dataset.dni || '';
            document.getElementById('edit_username').value = this.dataset.username || '';
            document.getElementById('edit_nombres').value = this.dataset.nombres || '';
            document.getElementById('edit_apellidos').value = this.dataset.apellidos || '';
            document.getElementById('edit_email').value = this.dataset.email || '';
            document.getElementById('edit_telefono').value = this.dataset.telefono || '';
            document.getElementById('edit_cargo').value = this.dataset.cargo || '';
            document.getElementById('edit_rol_id').value = this.dataset.rolId || '';
            document.getElementById('edit_unidad_id').value = this.dataset.unidadId || '';
            document.getElementById('edit_estado').value = this.dataset.estado || '1';
            editModal.show();
        });
    });
});
</script>
