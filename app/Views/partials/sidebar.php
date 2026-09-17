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

// Contador de notificaciones no leídas para badge del sidebar
$sidebarNotifCount = 0;
if ($user && class_exists('App\Models\Notificacion')) {
    $sidebarNotifCount = (new \App\Models\Notificacion())->getUnreadCount((int)$user['id'], $user['unidad_id'] ?? null);
}

// Descomponer nombre institucional para que nunca se corte con ellipsis
$brandMain = 'IESP TÚPAC AMARU';
$brandCity = 'CUSCO';
$rawShortName = trim($config['institucion_nombre_corto'] ?? 'IESP Túpac Amaru - Cusco');
if (preg_match('/^(.*?)\s*[-–—]\s*(.*)$/u', $rawShortName, $matches)) {
    $brandMain = mb_strtoupper(trim($matches[1]), 'UTF-8');
    $brandCity = mb_strtoupper(trim($matches[2]), 'UTF-8');
} else {
    $brandMain = mb_strtoupper($rawShortName, 'UTF-8');
}
?>

<aside class="admin-sidebar">
    <div class="sidebar-brand">
        <a href="<?= ViewHelper::url('/dashboard') ?>" class="sidebar-brand-link" title="<?= ViewHelper::escape($config['institucion_nombre'] ?? 'IESP Túpac Amaru - Cusco') ?>">
            <div class="sidebar-brand-logo-wrap">
                <?php if (!empty($logoPrincipal)): ?>
                    <img src="<?= $logoPrincipal ?>" alt="Logo Institucional" class="sidebar-brand-logo">
                <?php else: ?>
                    <div class="sidebar-brand-fallback">
                        <i class="bi bi-bank2"></i>
                    </div>
                <?php endif; ?>
            </div>
            <div class="sidebar-brand-text">
                <span class="sidebar-brand-institution"><?= ViewHelper::escape($brandMain) ?></span>
                <span class="sidebar-brand-meta">
                    <span class="sidebar-brand-badge"><?= ViewHelper::escape($brandCity) ?></span>
                    <span class="sidebar-brand-tag">Mesa de Partes</span>
                </span>
            </div>
        </a>
    </div>

    <div class="sidebar-menu">
        <div class="menu-category">Principal</div>
        <a href="<?= ViewHelper::url('/dashboard') ?>" class="nav-link-custom <?= ViewHelper::isActive('/dashboard') ?>">
            <i class="bi bi-speedometer2"></i>
            <span class="nav-label">Dashboard</span>
        </a>

        <div class="menu-category">Operaciones</div>
        <?php if ($canMesaPartes): ?>
            <a href="<?= ViewHelper::url('/mesa-partes') ?>" class="nav-link-custom <?= ViewHelper::isActive('/mesa-partes') ?>">
                <i class="bi bi-inbox-fill"></i>
                <span class="nav-label">Mesa de Partes</span>
            </a>
        <?php endif; ?>

        <?php if ($canDireccion): ?>
            <a href="<?= ViewHelper::url('/direccion') ?>" class="nav-link-custom <?= ViewHelper::isActive('/direccion') ?>">
                <i class="bi bi-diagram-3-fill"></i>
                <span class="nav-label">Dirección General</span>
            </a>
        <?php endif; ?>

        <a href="<?= ViewHelper::url('/expedientes') ?>" class="nav-link-custom <?= ViewHelper::isActive('/expedientes') ?>">
            <i class="bi bi-folder-fill"></i>
            <span class="nav-label">Todos los Expedientes</span>
        </a>

        <?php if ($hasUnidad): ?>
            <a href="<?= ViewHelper::url('/mi-unidad') ?>" class="nav-link-custom <?= ViewHelper::isActive('/mi-unidad') ?>">
                <i class="bi bi-building"></i>
                <span class="nav-label">Mi Unidad Orgánica</span>
            </a>
        <?php endif; ?>

        <a href="<?= ViewHelper::url('/notificaciones') ?>" class="nav-link-custom <?= ViewHelper::isActive('/notificaciones') ?>">
            <i class="bi bi-bell-fill"></i>
            <span class="nav-label">Notificaciones</span>
            <?php if ($sidebarNotifCount > 0): ?>
                <span class="badge rounded-pill bg-danger ms-auto nav-badge"><?= $sidebarNotifCount ?></span>
            <?php endif; ?>
        </a>

        <?php if ($canReportes): ?>
            <div class="menu-category">Inteligencia</div>
            <a href="<?= ViewHelper::url('/reportes') ?>" class="nav-link-custom <?= ViewHelper::isActive('/reportes') ?>">
                <i class="bi bi-graph-up-arrow"></i>
                <span class="nav-label">Reportes y Métricas</span>
            </a>
        <?php endif; ?>

        <?php if ($canAdmin): ?>
            <div class="menu-category">Administración</div>
            <a href="<?= ViewHelper::url('/administracion/usuarios') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/usuarios') ?>">
                <i class="bi bi-people-fill"></i>
                <span class="nav-label">Usuarios</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/roles') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/roles') ?>">
                <i class="bi bi-shield-lock-fill"></i>
                <span class="nav-label">Roles y Permisos</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/unidades') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/unidades') ?>">
                <i class="bi bi-buildings-fill"></i>
                <span class="nav-label">Unidades Orgánicas</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/programas') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/programas') ?>">
                <i class="bi bi-mortarboard-fill"></i>
                <span class="nav-label">Programas de Estudio</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/tramites') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/tramites') ?>">
                <i class="bi bi-card-checklist"></i>
                <span class="nav-label">Catálogo FUT</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/estados') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/estados') ?>">
                <i class="bi bi-tags-fill"></i>
                <span class="nav-label">Estados de Trámite</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/apariencia') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/apariencia') ?>">
                <i class="bi bi-palette-fill"></i>
                <span class="nav-label">Apariencia y Colores</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/configuracion') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/configuracion') ?>">
                <i class="bi bi-gear-fill"></i>
                <span class="nav-label">Configuración General</span>
            </a>
            <a href="<?= ViewHelper::url('/administracion/smtp') ?>" class="nav-link-custom <?= ViewHelper::isActive('/administracion/smtp') ?>">
                <i class="bi bi-envelope-at-fill"></i>
                <span class="nav-label">Servidor SMTP</span>
            </a>
        <?php endif; ?>

        <?php if ($canAuditoria): ?>
            <div class="menu-category">Seguridad</div>
            <a href="<?= ViewHelper::url('/auditoria') ?>" class="nav-link-custom <?= ViewHelper::isActive('/auditoria') ?>">
                <i class="bi bi-clock-history"></i>
                <span class="nav-label">Auditoría y Trazabilidad</span>
            </a>
        <?php endif; ?>
    </div>

    <div class="sidebar-footer">
        <div class="sidebar-footer-inner">
            <div class="status-pulse-container">
                <span class="status-dot-pulse"></span>
                <span>Sistema Operativo</span>
            </div>
            <span class="version-pill">v1.0</span>
        </div>
    </div>
</aside>
