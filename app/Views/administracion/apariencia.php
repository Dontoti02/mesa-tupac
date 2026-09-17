<?php
use App\Helpers\ViewHelper;
use App\Helpers\Csrf;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h3 class="fw-bold mb-1" style="color: var(--color-primary);">Personalización de Apariencia y Colores</h3>
        <p class="text-muted mb-0">Configuración visual basada en variables CSS institucionales con vista previa en tiempo real.</p>
    </div>
    <div>
        <button type="button" class="btn btn-outline-secondary fw-semibold shadow-sm me-2" id="btnPresetOficial">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Restaurar Paleta Oficial Túpac Amaru
        </button>
        <button type="submit" form="formApariencia" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-save me-1"></i> Guardar Cambios de Apariencia
        </button>
    </div>
</div>

<?php if ($flash = ViewHelper::flash('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i><?= ViewHelper::escape($flash) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Formulario de Colores -->
    <div class="col-lg-5">
        <form id="formApariencia" action="<?= ViewHelper::url('/administracion/apariencia') ?>" method="POST">
            <?= Csrf::field() ?>

            <!-- Paleta Primaria (Rojos Institucionales) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-circle-fill me-2" style="color: <?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>;"></i>
                        Paleta Principal Institucional (Rojos)
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color Primario (Encabezados, Botones Principales)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_primario" value="<?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>">
                            <input type="text" class="form-control font-monospace" name="color_primario" id="val_color_primario" value="<?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color Primario Oscuro (Estados Hover y Activos)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_primario_oscuro" value="<?= ViewHelper::escape($config['color_primario_oscuro'] ?? '#7F1D1D') ?>">
                            <input type="text" class="form-control font-monospace" name="color_primario_oscuro" id="val_color_primario_oscuro" value="<?= ViewHelper::escape($config['color_primario_oscuro'] ?? '#7F1D1D') ?>" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Color Primario Suave (Fondos de Alertas y Badges)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_primario_suave" value="<?= ViewHelper::escape($config['color_primario_suave'] ?? '#FEE2E2') ?>">
                            <input type="text" class="form-control font-monospace" name="color_primario_suave" id="val_color_primario_suave" value="<?= ViewHelper::escape($config['color_primario_suave'] ?? '#FEE2E2') ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paleta Secundaria (Naranjas Institucionales) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-circle-fill me-2" style="color: <?= ViewHelper::escape($config['color_secundario'] ?? '#F97316') ?>;"></i>
                        Paleta Secundaria / Acento (Naranjas)
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color Secundario (Llamadas de Atención y Acentos)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_secundario" value="<?= ViewHelper::escape($config['color_secundario'] ?? '#F97316') ?>">
                            <input type="text" class="form-control font-monospace" name="color_secundario" id="val_color_secundario" value="<?= ViewHelper::escape($config['color_secundario'] ?? '#F97316') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color Secundario Oscuro (Hover de Botones Acento)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_secundario_oscuro" value="<?= ViewHelper::escape($config['color_secundario_oscuro'] ?? '#C2410C') ?>">
                            <input type="text" class="form-control font-monospace" name="color_secundario_oscuro" id="val_color_secundario_oscuro" value="<?= ViewHelper::escape($config['color_secundario_oscuro'] ?? '#C2410C') ?>" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Color Secundario Suave (Estados En Trámite)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_secundario_suave" value="<?= ViewHelper::escape($config['color_secundario_suave'] ?? '#FFEDD5') ?>">
                            <input type="text" class="form-control font-monospace" name="color_secundario_suave" id="val_color_secundario_suave" value="<?= ViewHelper::escape($config['color_secundario_suave'] ?? '#FFEDD5') ?>" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Estructura y Neutros (Plomos y Fondos) -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold text-secondary">
                        <i class="bi bi-layout-sidebar-inset me-2 text-dark"></i>
                        Estructura y Colores Neutros (Plomos)
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color de Barra Lateral (Sidebar)</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_sidebar" value="<?= ViewHelper::escape($config['color_sidebar'] ?? '#374151') ?>">
                            <input type="text" class="form-control font-monospace" name="color_sidebar" id="val_color_sidebar" value="<?= ViewHelper::escape($config['color_sidebar'] ?? '#374151') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color de Texto de Enlaces del Sidebar</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_sidebar_texto" value="<?= ViewHelper::escape($config['color_sidebar_texto'] ?? '#F3F4F6') ?>">
                            <input type="text" class="form-control font-monospace" name="color_sidebar_texto" id="val_color_sidebar_texto" value="<?= ViewHelper::escape($config['color_sidebar_texto'] ?? '#F3F4F6') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Color de Fondo General del Sistema</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_fondo" value="<?= ViewHelper::escape($config['color_fondo'] ?? '#F3F4F6') ?>">
                            <input type="text" class="form-control font-monospace" name="color_fondo" id="val_color_fondo" value="<?= ViewHelper::escape($config['color_fondo'] ?? '#F3F4F6') ?>" required>
                        </div>
                    </div>
                    <div>
                        <label class="form-label fw-semibold small">Color de Bordes de Tarjetas y Tablas</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="picker_color_borde" value="<?= ViewHelper::escape($config['color_borde'] ?? '#D1D5DB') ?>">
                            <input type="text" class="form-control font-monospace" name="color_borde" id="val_color_borde" value="<?= ViewHelper::escape($config['color_borde'] ?? '#D1D5DB') ?>" required>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-light py-3 text-end">
                    <button type="submit" class="btn btn-primary fw-bold shadow-sm px-4">
                        <i class="bi bi-save me-1"></i> Guardar Configuración
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Live Preview Interactiva en Tiempo Real -->
    <div class="col-lg-7">
        <div class="sticky-top" style="top: 20px; z-index: 10;">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-display me-2 text-warning"></i>Vista Previa en Vivo (Live Preview)
                    </h6>
                    <span class="badge bg-success font-monospace">ACTUALIZACIÓN INSTANTÁNEA</span>
                </div>
                
                <!-- Mockup de Interfaz Dinámica -->
                <div id="livePreviewContainer" class="p-3" style="background-color: <?= ViewHelper::escape($config['color_fondo'] ?? '#F3F4F6') ?>; min-height: 480px; border-radius: 0 0 8px 8px;">
                    <!-- Simulación de Header -->
                    <div id="previewHeader" class="p-2 mb-3 bg-white border rounded shadow-xs d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-bank fs-4 me-2" id="previewHeaderIcon" style="color: <?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>;"></i>
                            <span class="fw-bold fs-6 text-dark">IESP TÚPAC AMARU – CUSCO</span>
                        </div>
                        <div>
                            <span class="badge" id="previewBadgeLive" style="background: <?= ViewHelper::escape($config['color_primario_suave'] ?? '#FEE2E2') ?>; color: <?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>;">
                                Online
                            </span>
                        </div>
                    </div>

                    <div class="row g-3">
                        <!-- Simulación de Sidebar -->
                        <div class="col-4">
                            <div id="previewSidebar" class="p-3 rounded shadow-xs h-100" style="background-color: <?= ViewHelper::escape($config['color_sidebar'] ?? '#374151') ?>; color: <?= ViewHelper::escape($config['color_sidebar_texto'] ?? '#F3F4F6') ?>;">
                                <div class="fw-bold mb-3 small opacity-75 text-uppercase">Navegación</div>
                                <div class="mb-2 p-2 rounded small fw-bold" id="previewSidebarActive" style="background: rgba(255,255,255,0.15); border-left: 3px solid <?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>;">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </div>
                                <div class="mb-2 p-2 rounded small opacity-75">
                                    <i class="bi bi-inbox me-1"></i> Mesa de Partes
                                </div>
                                <div class="p-2 rounded small opacity-75">
                                    <i class="bi bi-diagram-3 me-1"></i> Dirección
                                </div>
                            </div>
                        </div>

                        <!-- Simulación de Contenido y Botones -->
                        <div class="col-8">
                            <div class="card border mb-3 shadow-xs" id="previewCard" style="border-color: <?= ViewHelper::escape($config['color_borde'] ?? '#D1D5DB') ?>;">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold mb-1" id="previewTitle" style="color: <?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>;">
                                        Trámite EXP-2026-000001
                                    </h6>
                                    <p class="small text-muted mb-3">Solicitud de Certificado Modular Oficial</p>
                                    
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <button type="button" class="btn btn-sm text-white fw-bold shadow-xs" id="previewBtnPrimary" style="background-color: <?= ViewHelper::escape($config['color_primario'] ?? '#B3261E') ?>;">
                                            Botón Primario
                                        </button>
                                        <button type="button" class="btn btn-sm text-white fw-bold shadow-xs" id="previewBtnSecondary" style="background-color: <?= ViewHelper::escape($config['color_secundario'] ?? '#F97316') ?>;">
                                            Botón Acento
                                        </button>
                                    </div>

                                    <div class="p-2 rounded small" id="previewAlert" style="background-color: <?= ViewHelper::escape($config['color_secundario_suave'] ?? '#FFEDD5') ?>; color: <?= ViewHelper::escape($config['color_secundario_oscuro'] ?? '#C2410C') ?>;">
                                        <i class="bi bi-info-circle-fill me-1"></i> Expediente derivado a Unidad Académica.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mapa de vinculación Color Picker <-> Input Hex <-> Elementos de Vista Previa
    const colorBindings = [
        { picker: 'picker_color_primario', val: 'val_color_primario', handler: (c) => {
            document.getElementById('previewHeaderIcon').style.color = c;
            document.getElementById('previewTitle').style.color = c;
            document.getElementById('previewBtnPrimary').style.backgroundColor = c;
            document.getElementById('previewSidebarActive').style.borderLeftColor = c;
        }},
        { picker: 'picker_color_primario_suave', val: 'val_color_primario_suave', handler: (c) => {
            document.getElementById('previewBadgeLive').style.backgroundColor = c;
        }},
        { picker: 'picker_color_secundario', val: 'val_color_secundario', handler: (c) => {
            document.getElementById('previewBtnSecondary').style.backgroundColor = c;
        }},
        { picker: 'picker_color_secundario_suave', val: 'val_color_secundario_suave', handler: (c) => {
            document.getElementById('previewAlert').style.backgroundColor = c;
        }},
        { picker: 'picker_color_secundario_oscuro', val: 'val_color_secundario_oscuro', handler: (c) => {
            document.getElementById('previewAlert').style.color = c;
        }},
        { picker: 'picker_color_sidebar', val: 'val_color_sidebar', handler: (c) => {
            document.getElementById('previewSidebar').style.backgroundColor = c;
        }},
        { picker: 'picker_color_sidebar_texto', val: 'val_color_sidebar_texto', handler: (c) => {
            document.getElementById('previewSidebar').style.color = c;
        }},
        { picker: 'picker_color_fondo', val: 'val_color_fondo', handler: (c) => {
            document.getElementById('livePreviewContainer').style.backgroundColor = c;
        }},
        { picker: 'picker_color_borde', val: 'val_color_borde', handler: (c) => {
            document.getElementById('previewCard').style.borderColor = c;
        }}
    ];

    colorBindings.forEach(binding => {
        const picker = document.getElementById(binding.picker);
        const input = document.getElementById(binding.val);

        if (picker && input) {
            picker.addEventListener('input', function() {
                input.value = this.value;
                binding.handler(this.value);
            });
            input.addEventListener('input', function() {
                if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                    picker.value = this.value;
                    binding.handler(this.value);
                }
            });
        }
    });

    // Preset Oficial Túpac Amaru
    document.getElementById('btnPresetOficial')?.addEventListener('click', function() {
        const defaults = {
            'val_color_primario': '#B3261E',
            'picker_color_primario': '#B3261E',
            'val_color_primario_oscuro': '#7F1D1D',
            'picker_color_primario_oscuro': '#7F1D1D',
            'val_color_primario_suave': '#FEE2E2',
            'picker_color_primario_suave': '#FEE2E2',
            'val_color_secundario': '#F97316',
            'picker_color_secundario': '#F97316',
            'val_color_secundario_oscuro': '#C2410C',
            'picker_color_secundario_oscuro': '#C2410C',
            'val_color_secundario_suave': '#FFEDD5',
            'picker_color_secundario_suave': '#FFEDD5',
            'val_color_sidebar': '#374151',
            'picker_color_sidebar': '#374151',
            'val_color_sidebar_texto': '#F3F4F6',
            'picker_color_sidebar_texto': '#F3F4F6',
            'val_color_fondo': '#F3F4F6',
            'picker_color_fondo': '#F3F4F6',
            'val_color_borde': '#D1D5DB',
            'picker_color_borde': '#D1D5DB'
        };

        for (const [id, val] of Object.entries(defaults)) {
            const el = document.getElementById(id);
            if (el) el.value = val;
        }

        colorBindings.forEach(binding => {
            const val = document.getElementById(binding.val)?.value;
            if (val) binding.handler(val);
        });
    });
});
</script>
