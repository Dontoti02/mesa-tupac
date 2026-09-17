<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Configuración del Servidor SMTP</h3>
        <p class="text-muted mb-0">Parámetros de conexión para el envío automático de notificaciones por correo electrónico al ciudadano.</p>
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
    <!-- Configuración del Servidor -->
    <div class="col-lg-7">
        <form action="<?= ViewHelper::url('/administracion/smtp') ?>" method="POST">
            <?= Csrf::field() ?>
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-envelope-at me-2 text-primary"></i>Parámetros de Servidor Saliente (SMTP)
                    </h6>
                    <div>
                        <?php if (!empty($smtp['activo'])): ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                <i class="bi bi-check-circle-fill me-1"></i> Servicio Activo
                            </span>
                        <?php else: ?>
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                <i class="bi bi-pause-circle-fill me-1"></i> Modo Simulación Local
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Servidor Host SMTP <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="host" 
                                   value="<?= ViewHelper::escape($smtp['host'] ?? 'smtp.gmail.com') ?>" required placeholder="p. ej. smtp.gmail.com o mail.institucion.edu.pe">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Puerto <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="puerto" 
                                   value="<?= ViewHelper::escape((string)($smtp['puerto'] ?? 587)) ?>" required placeholder="587 / 465 / 25">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Protocolo de Cifrado / Seguridad <span class="text-danger">*</span></label>
                            <select class="form-select" name="seguridad" required>
                                <option value="tls" <?= ($smtp['seguridad'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS - Recomendado Puerto 587)</option>
                                <option value="ssl" <?= ($smtp['seguridad'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL (Puerto 465)</option>
                                <option value="ninguna" <?= ($smtp['seguridad'] ?? '') === 'ninguna' ? 'selected' : '' ?>>Ninguna / Texto Plano (Puerto 25)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Usuario / Cuenta de Autenticación</label>
                            <input type="text" class="form-control" name="usuario" 
                                   value="<?= ViewHelper::escape($smtp['usuario'] ?? '') ?>" placeholder="correo@institucion.edu.pe">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Contraseña o Clave de Aplicación</label>
                            <input type="password" class="form-control" name="password" placeholder="•••••••••••••••• (Dejar en blanco para mantener la clave actual)">
                            <div class="form-text">Para servidores como Gmail o Microsoft 365, use una <strong>Contraseña de Aplicación</strong> de 16 caracteres.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Correo Electrónico Remitente <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="remitente_email" 
                                   value="<?= ViewHelper::escape($smtp['remitente_email'] ?? 'mesadepartes@tupacamaru.edu.pe') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Visible del Remitente <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="remitente_nombre" 
                                   value="<?= ViewHelper::escape($smtp['remitente_nombre'] ?? 'Mesa de Partes - IESP Túpac Amaru') ?>" required>
                        </div>

                        <div class="col-md-12 pt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" name="activo" value="1" id="smtp_activo" <?= !empty($smtp['activo']) ? 'checked' : '' ?>>
                                <label class="form-check-label fw-bold" for="smtp_activo">
                                    Habilitar Envío Real de Correos SMTP
                                </label>
                                <div class="form-text">Si está desactivado, el sistema simulará el envío registrándolo en <code>storage/logs/mail.log</code> para no bloquear las operaciones locales.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-3 text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-1"></i> Guardar Parámetros SMTP
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Prueba de Envío de Correo -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="mb-0 fw-bold text-secondary">
                    <i class="bi bi-send-check me-2 text-success"></i>Diagnóstico y Prueba de Conexión
                </h6>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-3">
                    Envíe un correo de prueba para verificar que las credenciales, el puerto y el certificado SSL/TLS funcionen correctamente con el servidor de destino.
                </p>

                <form action="<?= ViewHelper::url('/administracion/smtp/probar') ?>" method="POST">
                    <?= Csrf::field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Dirección de Correo Destino <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" name="email_prueba" required placeholder="tu-correo@gmail.com">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-success fw-bold w-100 shadow-sm">
                        <i class="bi bi-broadcast-pin me-1"></i> Enviar Mensaje de Prueba
                    </button>
                </form>
            </div>
        </div>

        <!-- Instrucciones y Recomendaciones -->
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body p-4">
                <h6 class="fw-bold text-dark mb-2"><i class="bi bi-shield-check me-1 text-primary"></i> Recomendaciones de Seguridad</h6>
                <ul class="text-muted small mb-0 ps-3">
                    <li class="mb-2"><strong>Gmail:</strong> Active la verificación en dos pasos en su cuenta de Google y cree una "Contraseña de aplicaciones". Use el puerto <strong>587 (TLS)</strong>.</li>
                    <li class="mb-2"><strong>Microsoft 365 / Outlook:</strong> Host <code>smtp.office365.com</code>, puerto <code>587</code>, protocolo TLS.</li>
                    <li><strong>Servidor Local / XAMPP:</strong> Puede mantener el interruptor apagado para operar en modo simulación transparente sin errores de red.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
