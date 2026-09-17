<?php
use App\Helpers\Csrf;
use App\Helpers\ViewHelper;
?>

<div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle mb-2" style="width: 54px; height: 54px; color: var(--color-primary) !important;">
        <i class="bi bi-shield-lock-fill fs-3"></i>
    </div>
    <h5 class="fw-bold text-dark mb-1">Nueva Contraseña</h5>
    <p class="text-muted small mb-0">
        Para la cuenta: <strong><?= ViewHelper::escape($email) ?></strong>
    </p>
</div>

<form action="<?= ViewHelper::url('/restablecer-password') ?>" method="POST" autocomplete="off">
    <?= Csrf::field() ?>
    <input type="hidden" name="token" value="<?= ViewHelper::escape($token) ?>">

    <div class="mb-3">
        <label for="password" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Nueva Contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-key"></i>
            </span>
            <input type="password" 
                   class="form-control border-start-0 border-end-0 ps-0" 
                   id="password" 
                   name="password" 
                   placeholder="Mínimo 8 caracteres" 
                   required 
                   minlength="8" 
                   autofocus>
            <button class="btn btn-outline-secondary bg-light border-start-0 text-muted" 
                    type="button" 
                    id="togglePwd1" 
                    title="Mostrar/Ocultar contraseña">
                <i class="bi bi-eye" id="eyeIcon1"></i>
            </button>
        </div>
        <div class="form-text small text-muted">Use una combinación segura de letras, números y símbolos.</div>
    </div>

    <div class="mb-4">
        <label for="password_confirmacion" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Confirmar Nueva Contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-check2-circle"></i>
            </span>
            <input type="password" 
                   class="form-control border-start-0 border-end-0 ps-0" 
                   id="password_confirmacion" 
                   name="password_confirmacion" 
                   placeholder="Repita su nueva contraseña" 
                   required 
                   minlength="8">
            <button class="btn btn-outline-secondary bg-light border-start-0 text-muted" 
                    type="button" 
                    id="togglePwd2" 
                    title="Mostrar/Ocultar contraseña">
                <i class="bi bi-eye" id="eyeIcon2"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center mb-3">
        <i class="bi bi-check2-square me-2 fs-5"></i> Actualizar y Guardar Contraseña
    </button>

    <div class="text-center">
        <a href="<?= ViewHelper::url('/login') ?>" class="text-decoration-none small text-muted">
            <i class="bi bi-arrow-left me-1"></i> Cancelar y Volver al Login
        </a>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        function setupToggle(btnId, inputId, iconId) {
            const btn = document.getElementById(btnId);
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (btn && input && icon) {
                btn.addEventListener('click', function() {
                    const isPwd = input.getAttribute('type') === 'password';
                    input.setAttribute('type', isPwd ? 'text' : 'password');
                    icon.classList.toggle('bi-eye');
                    icon.classList.toggle('bi-eye-slash');
                });
            }
        }
        setupToggle('togglePwd1', 'password', 'eyeIcon1');
        setupToggle('togglePwd2', 'password_confirmacion', 'eyeIcon2');
    });
</script>
