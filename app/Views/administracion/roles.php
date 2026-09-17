<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Roles del Sistema y Matriz RBAC</h3>
        <p class="text-muted mb-0">Control de acceso basado en roles y permisos granulares por módulo.</p>
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

<div class="row g-4">
    <!-- Lista de Roles -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-secondary"><i class="bi bi-shield-lock me-2"></i>Roles Definidos</h6>
            </div>
            <div class="list-group list-group-flush" id="rolesListGroup" role="tablist">
                <?php foreach ($roles as $idx => $r): ?>
                    <a class="list-group-item list-group-item-action p-3 <?= $idx === 0 ? 'active' : '' ?>" 
                       id="tab-role-<?= $r['id'] ?>" 
                       data-bs-toggle="list" 
                       href="#role-content-<?= $r['id'] ?>" 
                       role="tab">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-bold"><?= ViewHelper::escape($r['nombre']) ?></h6>
                            <span class="badge <?= $idx === 0 ? 'bg-light text-dark' : 'bg-secondary' ?> font-monospace">
                                <?= ViewHelper::escape($r['slug']) ?>
                            </span>
                        </div>
                        <p class="mb-1 small opacity-75"><?= ViewHelper::escape($r['descripcion'] ?? 'Sin descripción') ?></p>
                        <small class="d-block mt-1">
                            <i class="bi bi-check2-all me-1"></i>
                            <?= count($permisosPorRol[$r['id']] ?? []) ?> permisos asignados
                        </small>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Matriz de Permisos por Rol -->
    <div class="col-lg-8">
        <div class="tab-content" id="rolesTabContent">
            <?php foreach ($roles as $idx => $r): 
                $activePerms = $permisosPorRol[$r['id']] ?? [];
                $isSuper = ($r['slug'] === 'superadmin');
            ?>
                <div class="tab-pane fade <?= $idx === 0 ? 'show active' : '' ?>" id="role-content-<?= $r['id'] ?>" role="tabpanel">
                    <form action="<?= ViewHelper::url('/administracion/roles/' . $r['id'] . '/permisos') ?>" method="POST">
                        <?= Csrf::field() ?>
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 fw-bold" style="color: var(--color-primary);">
                                        Permisos para: <?= ViewHelper::escape($r['nombre']) ?>
                                    </h6>
                                    <small class="text-muted">Slug identificador: <code><?= ViewHelper::escape($r['slug']) ?></code></small>
                                </div>
                                <?php if (!$isSuper): ?>
                                    <button type="submit" class="btn btn-sm btn-primary fw-bold shadow-sm">
                                        <i class="bi bi-save me-1"></i> Guardar Cambios
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div class="card-body p-4">
                                <?php if ($isSuper): ?>
                                    <div class="alert alert-info border-0 mb-4">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        El rol <strong>Super Administrador</strong> posee acceso universal e irrestricto a todos los módulos y acciones del sistema por diseño normativo.
                                    </div>
                                <?php endif; ?>

                                <?php foreach ($permisosAgrupados as $modulo => $permisos): ?>
                                    <div class="mb-4 pb-3 border-bottom">
                                        <h6 class="text-uppercase fw-bold text-secondary mb-3 small tracking-wide">
                                            <i class="bi bi-folder2-open me-2 text-primary"></i> Módulo: <?= ViewHelper::escape(strtoupper($modulo)) ?>
                                        </h6>
                                        <div class="row g-3">
                                            <?php foreach ($permisos as $p): 
                                                $checked = in_array((int)$p['id'], $activePerms, true) || $isSuper;
                                            ?>
                                                <div class="col-md-6">
                                                    <div class="form-check card p-2 border <?= $checked ? 'border-primary-subtle bg-light' : 'border-light' ?>">
                                                        <input class="form-check-input ms-0 me-2" 
                                                               type="checkbox" 
                                                               name="permisos[]" 
                                                               value="<?= $p['id'] ?>" 
                                                               id="perm_<?= $r['id'] ?>_<?= $p['id'] ?>"
                                                               <?= $checked ? 'checked' : '' ?>
                                                               <?= $isSuper ? 'disabled' : '' ?>>
                                                        <label class="form-check-label ps-1" for="perm_<?= $r['id'] ?>_<?= $p['id'] ?>">
                                                            <div class="fw-semibold text-dark small"><?= ViewHelper::escape($p['nombre']) ?></div>
                                                            <small class="text-muted font-monospace d-block" style="font-size: 0.75rem;">
                                                                <?= ViewHelper::escape($p['slug']) ?>
                                                            </small>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (!$isSuper): ?>
                                <div class="card-footer bg-light py-3 text-end">
                                    <button type="submit" class="btn btn-primary fw-bold shadow-sm">
                                        <i class="bi bi-save me-1"></i> Guardar Cambios para <?= ViewHelper::escape($r['nombre']) ?>
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
