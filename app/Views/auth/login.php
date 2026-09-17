<?php
use App\Helpers\Csrf;
use App\Helpers\ViewHelper;
use App\Core\Session;

$oldUsername = Session::getFlash('old_username', '');
?>

<form action="<?= ViewHelper::url('/login') ?>" method="POST" autocomplete="off">
    <?= Csrf::field() ?>

    <div class="mb-3">
        <label for="username" class="form-label fw-semibold small text-uppercase" style="letter-spacing: 0.5px;">Usuario / DNI / Correo</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-person-badge"></i>
            </span>
            <input type="text" 
                   class="form-control border-start-0 ps-0" 
                   id="username" 
                   name="username" 
                   value="<?= ViewHelper::escape($oldUsername) ?>" 
                   placeholder="Ingrese su usuario o DNI" 
                   required 
                   autofocus>
        </div>
    </div>

    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <label for="password" class="form-label fw-semibold small text-uppercase mb-0" style="letter-spacing: 0.5px;">Contraseña</label>
            <a href="<?= ViewHelper::url('/recuperar-password') ?>" class="small text-decoration-none text-muted" style="font-size: 0.8rem;">¿Olvidó su contraseña?</a>
        </div>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-shield-lock"></i>
            </span>
            <input type="password" 
                   class="form-control border-start-0 border-end-0 ps-0" 
                   id="password" 
                   name="password" 
                   placeholder="Ingrese su contraseña" 
                   required>
            <button class="btn btn-outline-secondary bg-light border-start-0 text-muted" 
                    type="button" 
                    id="togglePassword" 
                    title="Mostrar/Ocultar contraseña">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </button>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center">
        <i class="bi bi-box-arrow-in-right me-2 fs-5"></i> Ingresar al Sistema
    </button>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        if (toggleBtn && passwordInput && eyeIcon) {
            toggleBtn.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                eyeIcon.classList.toggle('bi-eye');
                eyeIcon.classList.toggle('bi-eye-slash');
            });
        }
    });
</script>
