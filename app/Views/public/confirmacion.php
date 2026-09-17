<?php
use App\Helpers\ViewHelper;
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden text-center">
                <div class="p-4" style="background: linear-gradient(135deg, #15803D 0%, #166534 100%); color: #ffffff;">
                    <div class="d-inline-flex p-3 rounded-circle bg-white text-success mb-3 shadow">
                        <i class="bi bi-check-circle-fill display-4"></i>
                    </div>
                    <h2 class="fw-bold mb-1">¡Trámite Registrado con Éxito!</h2>
                    <p class="mb-0 text-white-50">Su Formulario Único de Trámite ha sido recibido en la Mesa de Partes Virtual.</p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <div class="p-4 rounded-3 mb-4" style="background-color: var(--color-bg); border: 2px dashed var(--color-border);">
                        <div class="row g-3 align-items-center">
                            <div class="col-md-6 border-end-md">
                                <span class="text-muted small text-uppercase fw-bold">Número de Expediente</span>
                                <h3 class="fw-bold my-1" style="color: var(--color-primary);"><?= ViewHelper::escape($expediente['numero_expediente']) ?></h3>
                                <span class="badge bg-secondary-subtle text-secondary small">Oficial IESP Túpac Amaru</span>
                            </div>
                            <div class="col-md-6">
                                <span class="text-muted small text-uppercase fw-bold">Código de Seguimiento</span>
                                <h3 class="fw-bold my-1 text-warning font-monospace" style="color: var(--color-secondary) !important; letter-spacing: 2px;"><?= ViewHelper::escape($expediente['codigo_seguimiento']) ?></h3>
                                <span class="badge bg-warning-subtle text-warning-emphasis small">Clave Privada de Consulta</span>
                            </div>
                        </div>
                    </div>

                    <div class="text-start mb-4 bg-light p-3 rounded border">
                        <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-info-circle-fill text-primary me-2"></i>Información del Registro:</h6>
                        <ul class="list-unstyled mb-0 small text-muted d-flex flex-column gap-1">
                            <li><strong>Solicitante:</strong> <?= ViewHelper::escape($expediente['nombres'] . ' ' . $expediente['apellido_paterno'] . ' ' . $expediente['apellido_materno']) ?> (DNI: <?= ViewHelper::escape($expediente['dni']) ?>)</li>
                            <li><strong>Trámite:</strong> <?= ViewHelper::escape($expediente['tramite_nombre'] ?? $expediente['solicito']) ?></li>
                            <li><strong>Fecha y Hora:</strong> <?= ViewHelper::formatDateTime($expediente['fecha_ingreso']) ?> hrs.</li>
                            <li><strong>Ubicación Inicial:</strong> Mesa de Partes (Revisión y Conformidad)</li>
                            <li><strong>Correo de Notificación:</strong> <?= ViewHelper::escape($expediente['correo']) ?></li>
                        </ul>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                        <a href="<?= ViewHelper::url('/cargo/' . $expediente['id']) ?>" target="_blank" class="btn btn-primary btn-lg px-4 fw-bold">
                            <i class="bi bi-printer-fill me-2"></i> Ver / Imprimir Cargo Digital
                        </a>
                        <a href="<?= ViewHelper::url('/consulta?expediente=' . urlencode($expediente['numero_expediente']) . '&codigo=' . urlencode($expediente['codigo_seguimiento'])) ?>" class="btn btn-outline-secondary btn-lg px-4">
                            <i class="bi bi-search me-2"></i> Seguir este Trámite
                        </a>
                    </div>
                </div>

                <div class="card-footer bg-white border-top py-3 text-muted small">
                    ¿Desea registrar otra solicitud? <a href="<?= ViewHelper::url('/tramite') ?>" class="text-decoration-none fw-semibold" style="color: var(--color-primary);">Presentar un nuevo FUT</a>
                </div>
            </div>
        </div>
    </div>
</div>
