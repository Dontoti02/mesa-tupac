<?php
use App\Helpers\ViewHelper;
use App\Core\Session;
use App\Models\Configuracion;

$user = Session::get('user');
$config = Configuracion::getAll();
$instNombreCorto = $config['institucion_nombre_corto'] ?? 'IESP Túpac Amaru';
$logoPrincipal = !empty($config['logo_principal']) ? ViewHelper::url($config['logo_principal']) : '';

// Helper de permisos del usuario conectado
$permissions = $user['permissions'] ?? [];
$roles = $user['roles'] ?? [];
$isSuperadmin = in_array('superadmin', $roles, true);

$canMesaPartes = $isSuperadmin || in_array('mesa_partes', $roles, true) || in_array('expedientes.crear', $permissions, true);
$canDireccion = $isSuperadmin || in_array('direccion', $roles, true) || in_array('expedientes.derivar', $permissions, true);
$hasUnidad = !empty($user['unidad_id']);
$canReportes = $isSuperadmin || in_array('reportes.ver', $permissions, true);
$canAuditoria = $isSuperadmin || in_array('auditoria.ver', $permissions, true);
$canAdmin = $isSuperadmin || in_array('admin', $roles, true) || in_array('usuarios.ver', $permissions, true) || in_array('configuracion.general', $permissions, true);
?>

<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?= ViewHelper::url('/dashboard') ?>">
            <?php if (!empty($logoPrincipal)): ?>
                <img src="<?= $logoPrincipal ?>" alt="Logo" style="max-height: 38px;">
            <?php else: ?>
                <i class="bi bi-bank fs-3 text-warning"></i>
            <?php endif; ?>
            <span class="text-truncate"><?= ViewHelper::escape($instNombreCorto) ?></span>
        </a>
    </div>

    <div class="sidebar-menu">
        <div class="menu-category">Principal</div>
        <a href="<?= ViewHelper::url('/dashboard') ?>" class="nav-link-custom <?= ViewHelper::isActive('/dashboard') ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>

        <div class="menu-category">Operaciones</div>
        <?php if ($canMesaPartes): ?>
            <a href="<?= ViewHelper::url('/mesa-partes') ?>" class="nav-link-custom <?= ViewHelper::isActive('/mesa-partes') ?>">
                <i class="bi bi-inbox-fill"></i> Mesa de Partes
            </a>
        <?php endif; ?>

        <?php if ($canDireccion): ?>
            <a href="<?= ViewHelper::url('/direccion') ?>" class="nav-link-custom <?= ViewHelper::isActive('/direccion') ?>">
                <i class="bi bi-diagram-3-fill"></i> Dirección General
            </a>
        <?php endif; ?>

        <a href="<?= ViewHelper::url('/expedientes') ?>" class="nav-link-custom <?= ViewHelper::isActive('/expedientes') ?>">
            <i class="bi bi-folder-fill"></i> Todos los Expedientes
        </a>

        <?php if ($hasUnidad): ?>
            <a href="<?= ViewHelper::url('/mi-unidad') ?>" class="nav-link-custom <?= ViewHelper::isActive('/mi-unidad') ?>">
                <i class="bi bi-building"></i> Mi Unidad Orgánica
            </a>
        <?php endif; ?>

        <a href="<?= ViewHelper::url('/notificaciones') ?>" class="nav-link-custom <?= ViewHelper::isActive('/notificaciones') ?>">
            <i class="bi bi-bell-fill"></i> Notificaciones
        </a>

        <?php if ($canReportes): ?>
            <div class="menu-category">Inteligencia</div>
            <a href="<?= ViewHelper::url('/reportes') ?>" class="nav-link-custom <?= ViewHelper::isActive('/reportes') ?>">
                <i class="bi bi-graph-up-arrow"></i> Reportes y Métricas
            </a>
        <?php endif; ?>

        <?php if ($canAdmin): ?>
            <div class="menu-category">Administración</div>
            <a href="<?= ViewHelper::url('/administracion/usuarios') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/usuarios') ?>">
                <i class="bi bi-people-fill"></i> Usuarios
            </a>
            <a href="<?= ViewHelper::url('/administracion/roles') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/roles') ?>">
                <i class="bi bi-shield-lock-fill"></i> Roles y Permisos
            </a>
            <a href="<?= ViewHelper::url('/administracion/unidades') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/unidades') ?>">
                <i class="bi bi-buildings-fill"></i> Unidades Orgánicas
            </a>
            <a href="<?= ViewHelper::url('/administracion/programas') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/programas') ?>">
                <i class="bi bi-mortarboard-fill"></i> Programas de Estudio
            </a>
            <a href="<?= ViewHelper::url('/administracion/tramites') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/tramites') ?>">
                <i class="bi bi-card-checklist"></i> Catálogo FUT
            </a>
            <a href="<?= ViewHelper::url('/administracion/estados') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/estados') ?>">
                <i class="bi bi-tags-fill"></i> Estados de Trámite
            </a>
            <a href="<?= ViewHelper::url('/administracion/apariencia') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/apariencia') ?>">
                <i class="bi bi-palette-fill"></i> Apariencia y Colores
            </a>
            <a href="<?= ViewHelper::url('/administracion/configuracion') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/configuracion') ?>">
                <i class="bi bi-gear-fill"></i> Configuración General
            </a>
            <a href="<?= ViewHelper::url('/administracion/smtp') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/smtp') ?>">
                <i class="bi bi-envelope-at-fill"></i> Servidor SMTP
            </a>
        <?php endif; ?>

        <?php if ($canAuditoria): ?>
            <div class="menu-category">Seguridad</div>
            <a href="<?= ViewHelper::url('/auditoria') ?>" class="nav-link-custom <?= ViewHelper::isActive('/auditoria') ?>">
                <i class="bi bi-clock-history"></i> Auditoría y Trazabilidad
            </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer text-muted small">
        <div class="d-flex align-items-center justify-content-between">
            <span>Versión 1.0</span>
            <span class="badge bg-success-subtle text-success">Online</span>
        </div>
    </div>
</aside>
