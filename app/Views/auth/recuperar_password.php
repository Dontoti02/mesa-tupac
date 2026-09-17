<?php
use App\Helpers\Csrf;
use App\Helpers\ViewHelper;
use App\Core\Session;

$oldEmail = Session::getFlash('old_email', '');
$instNombreCorto = $config['institucion_nombre_corto'] ?? 'IESP Túpac Amaru';
?>

<?php if ($smtpActivo): ?>
    <!-- FORMULARIO DE RECUPERACIÓN (SMTP ACTIVO) -->
    <div class="text-center mb-4">
        <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 text-danger rounded-circle mb-2" style="width: 54px; height: 54px;">
            <i class="bi bi-key-fill fs-3 text-primary" style="color: var(--color-primary) !important;"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">¿Olvidó su contraseña?</h5>
        <p class="text-muted small mb-0">
            Ingrese su correo electrónico registrado. Le enviaremos un enlace seguro y temporal para restablecer su clave institucional.
        </p>
    </div>

    <form action="<?= ViewHelper::url('/recuperar-password') ?>" method="POST" autocomplete="off">
        <?= Csrf::field() ?>

        <div class="mb-4">
            <label for="email" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Correo Electrónico</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted">
                    <i class="bi bi-envelope"></i>
                </span>
                <input type="email" 
                       class="form-control border-start-0 ps-0" 
                       id="email" 
                       name="email" 
                       value="<?= ViewHelper::escape($oldEmail) ?>" 
                       placeholder="ejemplo@tupacamaru.edu.pe" 
                       required 
                       autofocus>
            </div>
            <div class="form-text small text-muted">
                Debe ser el correo institucional o personal vinculado a su usuario.
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center mb-3">
            <i class="bi bi-send me-2 fs-5"></i> Enviar Enlace de Recuperación
        </button>

        <div class="text-center">
            <a href="<?= ViewHelper::url('/login') ?>" class="text-decoration-none small text-muted">
                <i class="bi bi-arrow-left me-1"></i> Volver al Inicio de Sesión
            </a>
        </div>
    </form>

<?php else: ?>
    <!-- AVISO INSTITUCIONAL (SMTP INACTIVO) -->
    <div class="text-center mb-3">
        <div class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle mb-3" style="width: 60px; height: 60px;">
            <i class="bi bi-shield-exclamation fs-1 text-warning"></i>
        </div>
        <h5 class="fw-bold text-dark mb-1">Recuperación de Contraseña</h5>
        <span class="badge bg-secondary mb-3">Servicio SMTP Inactivo</span>
        <p class="text-muted small mb-3">
            El servicio automatizado de envío de correos electrónicos no se encuentra habilitado actualmente en este servidor institucional.
        </p>
    </div>

    <div class="card bg-light border-0 p-3 mb-4 text-start">
        <h6 class="fw-bold text-dark mb-2 small d-flex align-items-center">
            <i class="bi bi-info-circle-fill text-primary me-2" style="color: var(--color-primary) !important;"></i>
            Procedimiento de Asistencia
        </h6>
        <p class="small text-muted mb-2">
            Por estrictas políticas de seguridad informática del <strong><?= ViewHelper::escape($instNombreCorto) ?></strong>, para restablecer o desbloquear su cuenta debe coordinar directamente con el personal autorizado:
        </p>
        <ul class="small text-muted ps-3 mb-2">
            <li>Acercarse a la oficina de <strong>Soporte Técnico / Cómputo</strong> con su documento de identidad (DNI).</li>
            <li>O comunicarse mediante los canales oficiales de atención:</li>
        </ul>
        <div class="small bg-white p-2 rounded border border-light">
            <?php if (!empty($config['institucion_email'])): ?>
                <div class="text-truncate">
                    <i class="bi bi-envelope-at text-muted me-1"></i> <strong>Correo:</strong> <?= ViewHelper::escape($config['institucion_email']) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($config['institucion_telefono'])): ?>
                <div class="mt-1">
                    <i class="bi bi-telephone text-muted me-1"></i> <strong>Teléfono:</strong> <?= ViewHelper::escape($config['institucion_telefono']) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($config['institucion_horario'])): ?>
                <div class="mt-1">
                    <i class="bi bi-clock text-muted me-1"></i> <strong>Horario:</strong> <?= ViewHelper::escape($config['institucion_horario']) ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <a href="<?= ViewHelper::url('/login') ?>" class="btn btn-outline-secondary w-100 py-2 d-flex align-items-center justify-content-center">
        <i class="bi bi-arrow-left me-2"></i> Volver al Inicio de Sesión
    </a>
<?php endif; ?>
