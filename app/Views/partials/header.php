<?php
use App\Helpers\ViewHelper;
use App\Core\Session;

$user = Session::get('user');
$unreadNotifCount = 0;
// Si hay usuario, podríamos consultar notificaciones no leídas
if ($user && class_exists('App\Models\Notificacion')) {
    $notifModel = new \App\Models\Notificacion();
    $unreadNotifCount = $notifModel->getUnreadCount((int)$user['id'], $user['unidad_id'] ?? null);
}
?>

<header class="admin-header">
    <div class="d-flex align-items-center gap-3">
        <button type="button" class="btn btn-outline-secondary d-lg-none py-1 px-2 border-0" id="sidebarToggle">
            <i class="bi bi-list fs-3"></i>
        </button>
        <h5 class="mb-0 fw-bold d-none d-md-block text-truncate" style="max-width: 400px; color: var(--color-dark);">
            <?= ViewHelper::escape($title ?? 'Panel Institucional') ?>
        </h5>
    </div>

    <div class="d-flex align-items-center gap-3">
        <!-- Notificaciones -->
        <a href="<?= ViewHelper::url('/notificaciones') ?>" class="btn btn-light position-relative rounded-circle p-2" title="Notificaciones">
            <i class="bi bi-bell fs-5 text-secondary"></i>
            <?php if ($unreadNotifCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                    <?= $unreadNotifCount ?>
                </span>
            <?php endif; ?>
        </a>

        <!-- Portal Público Directo -->
        <a href="<?= ViewHelper::url('/') ?>" target="_blank" class="btn btn-outline-secondary btn-sm d-none d-sm-inline-flex align-items-center gap-1">
            <i class="bi bi-box-arrow-up-right"></i> Portal Público
        </a>

        <!-- Perfil de Usuario -->
        <div class="dropdown">
            <button class="btn btn-light d-flex align-items-center gap-2 py-1 px-2 rounded-pill border" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.85rem; background-color: var(--color-primary) !important;">
                    <?= strtoupper(substr($user['nombres'] ?? 'U', 0, 1)) ?>
                </div>
                <div class="text-start d-none d-md-block pe-1">
                    <div class="fw-semibold small text-truncate" style="max-width: 140px;"><?= ViewHelper::escape($user['nombres'] ?? 'Usuario') ?></div>
                    <div class="text-muted" style="font-size: 0.72rem;"><?= ViewHelper::escape($user['cargo'] ?? 'Funcionario') ?></div>
                </div>
                <i class="bi bi-chevron-down small text-muted"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-2" style="min-width: 220px;">
                <li class="px-3 py-2 border-bottom mb-1 bg-light">
                    <div class="fw-bold text-truncate"><?= ViewHelper::escape($user['nombre_completo'] ?? '') ?></div>
                    <div class="small text-muted text-truncate"><?= ViewHelper::escape($user['email'] ?? '') ?></div>
                    <?php if (!empty($user['unidad_nombre'])): ?>
                        <span class="badge bg-secondary-subtle text-secondary mt-1 small"><?= ViewHelper::escape($user['unidad_nombre']) ?></span>
                    <?php endif; ?>
                </li>
                <li>
                    <a class="dropdown-item py-2" href="<?= ViewHelper::url('/cambiar-password') ?>">
                        <i class="bi bi-shield-lock me-2 text-muted"></i> Cambiar Contraseña
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item py-2 text-danger" href="<?= ViewHelper::url('/logout') ?>">
                        <i class="bi bi-box-arrow-right me-2 text-danger"></i> Cerrar Sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
