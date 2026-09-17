<?php
use App\Helpers\Csrf;
use App\Helpers\ViewHelper;
?>

<div class="mb-3 text-center">
    <div class="d-inline-flex p-3 rounded-circle bg-warning-subtle text-warning mb-2">
        <i class="bi bi-key-fill fs-3"></i>
    </div>
    <h5 class="fw-bold mb-1">Actualizar Contraseña</h5>
    <p class="text-muted small">Por motivos de seguridad, debe ingresar una nueva contraseña personal de al menos 8 caracteres.</p>
</div>

<form action="<?= ViewHelper::url('/cambiar-password') ?>" method="POST" autocomplete="off">
    <?= Csrf::field() ?>

    <div class="mb-3">
        <label for="password_actual" class="form-label fw-semibold small text-uppercase">Contraseña Actual</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-lock"></i>
            </span>
            <input type="password" 
                   class="form-control border-start-0 ps-0" 
                   id="password_actual" 
                   name="password_actual" 
                   placeholder="Ingrese su contraseña actual" 
                   required>
        </div>
    </div>

    <div class="mb-3">
        <label for="password_nuevo" class="form-label fw-semibold small text-uppercase">Nueva Contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-shield-check"></i>
            </span>
            <input type="password" 
                   class="form-control border-start-0 ps-0" 
                   id="password_nuevo" 
                   name="password_nuevo" 
                   placeholder="Mínimo 8 caracteres" 
                   minlength="8" 
                   required>
        </div>
    </div>

    <div class="mb-4">
        <label for="password_confirmacion" class="form-label fw-semibold small text-uppercase">Confirmar Nueva Contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted">
                <i class="bi bi-check2-circle"></i>
            </span>
            <input type="password" 
                   class="form-control border-start-0 ps-0" 
                   id="password_confirmacion" 
                   name="password_confirmacion" 
                   placeholder="Repita la nueva contraseña" 
                   minlength="8" 
                   required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary w-100 py-2 d-flex align-items-center justify-content-center">
        <i class="bi bi-shield-lock-fill me-2"></i> Actualizar y Continuar
    </button>
</form>
