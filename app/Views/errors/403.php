<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0 p-4">
                <div class="card-body">
                    <h1 class="display-1 fw-bold text-danger mb-3">403</h1>
                    <h3 class="fw-semibold mb-3">Acceso Denegado</h3>
                    <p class="text-muted mb-4">No tiene los permisos suficientes para acceder a este módulo institucional.</p>
                    <?php if (!empty($permission)): ?>
                        <div class="alert alert-secondary py-2 small mb-4">
                            Detalle: <code><?= \App\Helpers\ViewHelper::escape($permission) ?></code>
                        </div>
                    <?php endif; ?>
                    <a href="<?= \App\Helpers\ViewHelper::url('/dashboard') ?>" class="btn btn-primary px-4">
                        <i class="bi bi-speedometer2 me-2"></i>Ir al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
