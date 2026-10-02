<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Configuración Institucional General</h3>
        <p class="text-muted mb-0">Identidad institucional, membretes oficiales, datos de contacto, logos y numeración oficial de expedientes.</p>
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

<form action="<?= ViewHelper::url('/administracion/configuracion') ?>" method="POST" enctype="multipart/form-data">
    <?= Csrf::field() ?>

    <div class="row g-4">
        <!-- Identidad Institucional -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-bank me-2 text-primary"></i>Datos de Identidad de la Entidad
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Razón Social / Denominación Institucional Completa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="institucion_nombre" 
                                   value="<?= ViewHelper::escape($config['institucion_nombre'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre Corto / Siglas <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="institucion_nombre_corto" 
                                   value="<?= ViewHelper::escape($config['institucion_nombre_corto'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">RUC Institucional <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="institucion_ruc" maxlength="11" 
                                   value="<?= ViewHelper::escape($config['institucion_ruc'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Entidad Superior de Dependencia</label>
                            <input type="text" class="form-control" name="institucion_dependencia" 
                                   value="<?= ViewHelper::escape($config['institucion_dependencia'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Resolución de Creación / Licenciamiento</label>
                            <input type="text" class="form-control" name="institucion_resolucion" 
                                   value="<?= ViewHelper::escape($config['institucion_resolucion'] ?? '') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Denominación Oficial del Año</label>
                            <input type="text" class="form-control" name="institucion_anio" 
                                   value="<?= ViewHelper::escape($config['institucion_anio'] ?? '') ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Pie de Página Institucional (Documentos y Cargos)</label>
                            <textarea class="form-control" name="institucion_pie_pagina" rows="2"><?= ViewHelper::escape($config['institucion_pie_pagina'] ?? '') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Datos de Contacto y Ubicación -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-geo-alt me-2 text-primary"></i>Ubicación y Canales de Atención
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Dirección de Sede Central</label>
                            <input type="text" class="form-control" name="institucion_direccion" 
                                   value="<?= ViewHelper::escape($config['institucion_direccion'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ciudad Sede</label>
                            <input type="text" class="form-control" name="institucion_ciudad" 
                                   value="<?= ViewHelper::escape($config['institucion_ciudad'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Región / Departamento</label>
                            <input type="text" class="form-control" name="institucion_region" 
                                   value="<?= ViewHelper::escape($config['institucion_region'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Teléfonos de Atención</label>
                            <input type="text" class="form-control" name="institucion_telefono" 
                                   value="<?= ViewHelper::escape($config['institucion_telefono'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Correo Electrónico de Mesa</label>
                            <input type="email" class="form-control" name="institucion_correo" 
                                   value="<?= ViewHelper::escape($config['institucion_correo'] ?? '') ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Sitio Web Oficial</label>
                            <input type="url" class="form-control" name="institucion_web" 
                                   value="<?= ViewHelper::escape($config['institucion_web'] ?? '') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Numeración de Expedientes -->
            <div class="card shadow-sm border-0 mt-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-upc-scan me-2 text-primary"></i>Numeración y Correlativo de Expedientes
                    </h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">
                        Define el formato oficial con el que se numerarán los expedientes que se registren a partir de ahora.
                        Los expedientes ya registrados conservan su número original.
                    </p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="exp_num_sigla">
                                Sigla / Prefijo <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control text-uppercase" id="exp_num_sigla" name="expediente_num_sigla"
                                   maxlength="20" pattern="[A-Za-z0-9]+(-[A-Za-z0-9]+)*"
                                   value="<?= ViewHelper::escape($numeracion['sigla']) ?>" required>
                            <div class="form-text">Solo letras, números y guiones. Ej.: <code>EXP</code> o <code>MP-TA</code>.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="exp_num_digitos">
                                Cantidad de Dígitos del Correlativo <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="exp_num_digitos" name="expediente_num_digitos"
                                   min="1" max="12" step="1"
                                   value="<?= (int)$numeracion['digitos'] ?>" required>
                            <div class="form-text">Los ceros a la izquierda completan el número. Ej.: <code>6</code> → <code>000123</code>.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold" for="exp_num_inicio">
                                Número Inicial del Correlativo <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control" id="exp_num_inicio" name="expediente_num_inicio"
                                   min="1" step="1"
                                   value="<?= (int)$numeracion['inicio'] ?>" required>
                            <div class="form-text">Se aplica al iniciar un correlativo nuevo; no altera los ya emitidos.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold d-block">Año en el Número</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="exp_num_incluir_anio"
                                       name="expediente_num_incluir_anio" value="1"
                                       <?= $numeracion['incluir_anio'] ? 'checked' : '' ?>>
                                <label class="form-check-label" for="exp_num_incluir_anio">
                                    Incluir el año (reinicia el correlativo cada año)
                                </label>
                            </div>
                            <div class="form-text">Si lo desactiva, el correlativo será único para toda la vida del sistema.</div>
                        </div>
                        <div class="col-12">
                            <div class="border rounded p-3 bg-light d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
                                <div>
                                    <div class="small text-muted mb-1">Vista previa del próximo número</div>
                                    <code id="numeracion-preview" class="fs-5" data-proximo="<?= (int)$proximoCorrelativo ?>"><?= ViewHelper::escape($proximoNumero) ?></code>
                                </div>
                                <div class="small text-muted text-md-end">
                                    Correlativo <span id="numeracion-preview-correlativo"><?= (int)$proximoCorrelativo ?></span><br>
                                    Vigente desde el <?= ViewHelper::escape($numeracion['anio']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-3 d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                    <span class="small text-muted"><i class="bi bi-info-circle me-1"></i>Los cambios quedan registrados en la bitácora de auditoría.</span>
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-1"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>

        <!-- Archivos y Logos -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-image me-2 text-primary"></i>Logotipos del Sistema
                    </h6>
                </div>
                <div class="card-body p-4">
                    <!-- Logo Principal -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-semibold d-block text-start">Logo Principal (Sidebar y Header)</label>
                        <div class="p-3 bg-light border rounded mb-2 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                            <?php if (!empty($config['logo_principal'])): ?>
                                <img src="<?= ViewHelper::url($config['logo_principal']) ?>" alt="Logo Principal" style="max-height: 60px; max-width: 100%;">
                            <?php else: ?>
                                <span class="text-muted small"><i class="bi bi-image fs-4 d-block mb-1"></i>Sin logo configurado</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control form-control-sm" name="logo_principal" accept="image/*">
                    </div>

                    <!-- Logo Login -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-semibold d-block text-start">Logo para Pantalla de Acceso (Login)</label>
                        <div class="p-3 bg-light border rounded mb-2 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                            <?php if (!empty($config['logo_login'])): ?>
                                <img src="<?= ViewHelper::url($config['logo_login']) ?>" alt="Logo Login" style="max-height: 60px; max-width: 100%;">
                            <?php else: ?>
                                <span class="text-muted small"><i class="bi bi-image fs-4 d-block mb-1"></i>Sin logo configurado</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control form-control-sm" name="logo_login" accept="image/*">
                    </div>

                    <!-- Logo Documentos -->
                    <div class="mb-4 text-center">
                        <label class="form-label fw-semibold d-block text-start">Logo para Cargos y Documentos Oficiales</label>
                        <div class="p-3 bg-light border rounded mb-2 d-flex align-items-center justify-content-center" style="min-height: 80px;">
                            <?php if (!empty($config['logo_documentos'])): ?>
                                <img src="<?= ViewHelper::url($config['logo_documentos']) ?>" alt="Logo Documentos" style="max-height: 60px; max-width: 100%;">
                            <?php else: ?>
                                <span class="text-muted small"><i class="bi bi-image fs-4 d-block mb-1"></i>Sin logo configurado</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control form-control-sm" name="logo_documentos" accept="image/*">
                    </div>

                    <!-- Favicon -->
                    <div class="text-center">
                        <label class="form-label fw-semibold d-block text-start">Favicon del Navegador (.ico, .png)</label>
                        <div class="p-2 bg-light border rounded mb-2 d-flex align-items-center justify-content-center" style="min-height: 48px;">
                            <?php if (!empty($config['favicon'])): ?>
                                <img src="<?= ViewHelper::url($config['favicon']) ?>" alt="Favicon" style="height: 32px; width: 32px;">
                            <?php else: ?>
                                <span class="text-muted small">Sin favicon</span>
                            <?php endif; ?>
                        </div>
                        <input type="file" class="form-control form-control-sm" name="favicon" accept=".ico,.png,.svg">
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
(function () {
    var inputs = {
        sigla: document.getElementById('exp_num_sigla'),
        digitos: document.getElementById('exp_num_digitos'),
        anio: document.getElementById('exp_num_incluir_anio')
    };
    var preview = document.getElementById('numeracion-preview');
    var previewCorrelativo = document.getElementById('numeracion-preview-correlativo');
    if (!preview || !previewCorrelativo || !inputs.sigla || !inputs.digitos || !inputs.anio) {
        return;
    }

    var anioVigente = <?= json_encode((string)$numeracion['anio']) ?>;

    function actualizarVistaPrevia() {
        var sigla = inputs.sigla.value.trim().toUpperCase();
        if (!sigla) {
            sigla = 'EXP';
        }

        var digitos = parseInt(inputs.digitos.value, 10);
        if (isNaN(digitos) || digitos < 1) {
            digitos = 1;
        }
        if (digitos > 12) {
            digitos = 12;
        }

        var correlativo = parseInt(preview.dataset.proximo, 10);
        if (isNaN(correlativo)) {
            correlativo = 1;
        }

        var partes = [sigla];
        if (inputs.anio.checked) {
            partes.push(anioVigente);
        }
        partes.push(String(correlativo).padStart(digitos, '0'));

        preview.textContent = partes.join('-');
        previewCorrelativo.textContent = correlativo;
    }

    Object.keys(inputs).forEach(function (clave) {
        inputs[clave].addEventListener('input', actualizarVistaPrevia);
        inputs[clave].addEventListener('change', actualizarVistaPrevia);
    });
})();
</script>
