<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Centro de Notificaciones</h3>
        <p class="text-muted mb-0">Alertas de recepción de expedientes, derivaciones, observaciones y respuestas técnicas.</p>
    </div>
    <?php if ($unreadCount > 0): ?>
        <div>
            <form action="<?= ViewHelper::url('/notificaciones/marcar-leidas') ?>" method="POST">
                <?= Csrf::field() ?>
                <button type="submit" class="btn btn-outline-secondary fw-semibold shadow-sm">
                    <i class="bi bi-check2-all me-1"></i> Marcar todas como leídas
                </button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php if ($flash = ViewHelper::flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= ViewHelper::escape($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-bold text-secondary">
            <i class="bi bi-bell me-2"></i>Notificaciones Recientes (<?= count($notificaciones) ?>)
        </h6>
        <?php if ($unreadCount > 0): ?>
            <span class="badge bg-danger rounded-pill"><?= $unreadCount ?> sin leer</span>
        <?php endif; ?>
    </div>
    <div class="list-group list-group-flush">
        <?php if (empty($notificaciones)): ?>
            <div class="p-5 text-center text-muted">
                <i class="bi bi-bell-slash fs-1 d-block mb-2 text-secondary opacity-50"></i>
                No tiene notificaciones pendientes.
            </div>
        <?php else: ?>
            <?php foreach ($notificaciones as $n): 
                $isUnread = ((int)$n['leido'] === 0);
            ?>
                <div class="list-group-item p-4 <?= $isUnread ? 'bg-light border-start border-4 border-danger' : '' ?>">
                    <div class="d-flex w-100 justify-content-between align-items-start mb-1">
                        <div class="d-flex align-items-center">
                            <?php if ($n['tipo'] === 'success'): ?>
                                <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
                            <?php elseif ($n['tipo'] === 'warning'): ?>
                                <i class="bi bi-exclamation-triangle-fill text-warning fs-5 me-2"></i>
                            <?php elseif ($n['tipo'] === 'danger'): ?>
                                <i class="bi bi-x-circle-fill text-danger fs-5 me-2"></i>
                            <?php else: ?>
                                <i class="bi bi-info-circle-fill text-primary fs-5 me-2"></i>
                            <?php endif; ?>
                            <h6 class="mb-0 fw-bold <?= $isUnread ? 'text-dark' : 'text-secondary' ?>">
                                <?= ViewHelper::escape($n['titulo']) ?>
                            </h6>
                            <?php if ($isUnread): ?>
                                <span class="badge bg-danger ms-2" style="font-size: 0.65rem;">NUEVO</span>
                            <?php endif; ?>
                        </div>
                        <small class="text-muted"><?= ViewHelper::formatDate($n['created_at'], true) ?></small>
                    </div>
                    <p class="mb-2 text-secondary ps-4">
                        <?= ViewHelper::escape($n['mensaje']) ?>
                    </p>
                    <?php if (!empty($n['url'])): ?>
                        <div class="ps-4">
                            <a href="<?= ViewHelper::url($n['url']) ?>" class="btn btn-sm btn-outline-primary fw-semibold">
                                Ver Expediente <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
