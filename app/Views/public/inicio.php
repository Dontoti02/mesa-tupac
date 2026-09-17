<?php
use App\Helpers\ViewHelper;
?>

<!-- Hero Banner -->
<section class="hero-banner">
    <div class="container text-center py-4">
        <span class="badge bg-white text-danger px-3 py-2 fw-semibold mb-3 text-uppercase shadow-sm" style="letter-spacing: 1px;">
            Plataforma Digital Oficial
        </span>
        <h1 class="display-5 fw-bold mb-3">Mesa de Partes Virtual</h1>
        <p class="lead mb-4 mx-auto" style="max-width: 750px; opacity: 0.95;">
            Presente solicitudes, trámites académicos y administrativos desde cualquier lugar con validez oficial, trazabilidad en tiempo real y cargo de recepción digital.
        </p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="<?= ViewHelper::url('/tramite') ?>" class="btn btn-warning btn-lg px-4 fw-bold shadow-sm" style="background-color: var(--color-secondary); border-color: var(--color-secondary); color: #ffffff;">
                <i class="bi bi-file-earmark-plus me-2"></i> Presentar FUT Digital
            </a>
            <a href="<?= ViewHelper::url('/consulta') ?>" class="btn btn-outline-light btn-lg px-4 fw-semibold">
                <i class="bi bi-search me-2"></i> Consultar Expediente
            </a>
        </div>
    </div>
</section>

<!-- Tarjetas de Acciones Principales -->
<section class="container py-5">
    <div class="row g-4">
        <!-- 1. Trámite FUT -->
        <div class="col-md-4">
            <div class="portal-card h-100 p-4 d-flex flex-column">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" 
                     style="width: 54px; height: 54px; background-color: var(--color-primary-light); color: var(--color-primary);">
                    <i class="bi bi-pencil-square fs-3"></i>
                </div>
                <h4 class="fw-bold mb-2" style="color: var(--color-primary);">Registrar Trámite (FUT)</h4>
                <p class="text-muted small mb-4 flex-grow-1">
                    Complete el Formulario Único de Trámite en línea, adjunte sus documentos en PDF o imagen y obtenga su cargo oficial de inmediato.
                </p>
                <a href="<?= ViewHelper::url('/tramite') ?>" class="btn btn-primary w-100 py-2">
                    Iniciar Trámite <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- 2. Consulta de Estado -->
        <div class="col-md-4">
            <div class="portal-card h-100 p-4 d-flex flex-column">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3" 
                     style="width: 54px; height: 54px; background-color: var(--color-secondary-light); color: var(--color-secondary);">
                    <i class="bi bi-search fs-3"></i>
                </div>
                <h4 class="fw-bold mb-2" style="color: var(--color-secondary);">Consultar Expediente</h4>
                <p class="text-muted small mb-4 flex-grow-1">
                    Verifique la ubicación actual, la oficina responsable y la línea de tiempo de atención de su expediente mediante su código de seguimiento.
                </p>
                <a href="<?= ViewHelper::url('/consulta') ?>" class="btn btn-outline-secondary w-100 py-2">
                    Buscar Expediente <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>

        <!-- 3. Catálogo de Trámites -->
        <div class="col-md-4">
            <div class="portal-card h-100 p-4 d-flex flex-column">
                <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-3 bg-light text-dark" 
                     style="width: 54px; height: 54px;">
                    <i class="bi bi-journal-text fs-3 text-secondary"></i>
                </div>
                <h4 class="fw-bold mb-2">Procedimientos y Requisitos</h4>
                <p class="text-muted small mb-4 flex-grow-1">
                    Consulte los requisitos, plazos de atención y costos de los trámites de titulación, constancias, convalidaciones y certificaciones.
                </p>
                <a href="<?= ViewHelper::url('/tramite#catalogo') ?>" class="btn btn-outline-dark w-100 py-2">
                    Ver Catálogo FUT <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Pasos del Trámite -->
<section class="bg-white py-5 border-top border-bottom">
    <div class="container">
        <div class="text-center mb-5">
            <h3 class="fw-bold" style="color: var(--color-primary);">¿Cómo funciona la Mesa de Partes Virtual?</h3>
            <p class="text-muted">Proceso transparente y normado para la atención de sus requerimientos institucionales</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="p-3">
                    <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-4" 
                         style="width: 60px; height: 60px; color: var(--color-primary);">1</div>
                    <h6 class="fw-bold mb-2">Registro Virtual</h6>
                    <p class="small text-muted mb-0">Llena sus datos y adjunta los recaudos requeridos en formato digital.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-4" 
                         style="width: 60px; height: 60px; color: var(--color-secondary);">2</div>
                    <h6 class="fw-bold mb-2">Mesa de Partes</h6>
                    <p class="small text-muted mb-0">Verifica el cumplimiento de requisitos y remite a Dirección General.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-4" 
                         style="width: 60px; height: 60px; color: #0D9488;">3</div>
                    <h6 class="fw-bold mb-2">Atención en Unidad</h6>
                    <p class="small text-muted mb-0">La unidad competente recepciona y emite el informe de respuesta técnica.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="p-3">
                    <div class="rounded-circle bg-light border d-inline-flex align-items-center justify-content-center mb-3 fw-bold fs-4" 
                         style="width: 60px; height: 60px; color: #16A34A;">4</div>
                    <h6 class="fw-bold mb-2">Respuesta y Notificación</h6>
                    <p class="small text-muted mb-0">Dirección aprueba el trámite y se le notifica la conclusión oficial.</p>
                </div>
            </div>
        </div>
    </div>
</section>
