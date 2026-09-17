<?php
use App\Helpers\ViewHelper;
?>

<!-- Hero Banner del Catálogo -->
<section class="hero-banner py-4 mb-4">
    <div class="container text-center">
        <span class="badge bg-warning text-dark font-monospace mb-2 px-3 py-1 fw-bold">CATÁLOGO INSTITUCIONAL TUPA / FUT</span>
        <h2 class="fw-bold mb-2 text-white">Procedimientos, Requisitos y Plazos de Atención</h2>
        <p class="text-white opacity-90 max-w-700 mx-auto mb-0" style="max-width: 680px; font-size: 0.95rem;">
            Consulte la base normativa de trámites disponibles para estudiantes, egresados y ciudadanía en general. Verifique requisitos antes de iniciar su solicitud digital.
        </p>
    </div>
</section>

<div class="container mb-5">
    <!-- Barra de Búsqueda y Filtros de Categoría -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-3 p-md-4 bg-light">
            <div class="row g-3 align-items-center">
                <div class="col-lg-6">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="searchTramite" class="form-control border-start-0" placeholder="Buscar por nombre de trámite, código o palabra clave (ej. Certificado, Título, Matrícula)...">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="d-flex align-items-center gap-2 justify-content-lg-end">
                        <label class="small fw-bold text-secondary mb-0 text-nowrap"><i class="bi bi-funnel me-1"></i>Categoría:</label>
                        <select id="filterCategoria" class="form-select form-select-sm" style="max-width: 320px;">
                            <option value="todos">-- Todas las Categorías (<?= count($tramites) ?> trámites) --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= ViewHelper::escape($cat['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contador de Resultados -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold text-dark mb-0">
            <i class="bi bi-journal-check me-2" style="color: var(--color-primary);"></i>Trámites Disponibles
        </h5>
        <span class="badge bg-white text-secondary border px-3 py-2" id="tramitesCount">
            Mostrando <?= count($tramites) ?> procedimientos
        </span>
    </div>

    <!-- Rejilla de Tarjetas de Trámites -->
    <div class="row g-4" id="tramitesGrid">
        <?php if (empty($tramites)): ?>
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                No se encontraron trámites registrados en el catálogo institucional.
            </div>
        <?php else: ?>
            <?php foreach ($tramites as $t): 
                $costo = (float)($t['costo'] ?? 0);
                $plazo = (int)($t['plazo_dias'] ?? 15);
                $reqs = !empty($t['requisitos']) ? explode("\n", $t['requisitos']) : [];
            ?>
                <div class="col-md-6 col-lg-4 tramite-item" 
                     data-cat="<?= $t['categoria_id'] ?>" 
                     data-search="<?= strtolower(ViewHelper::escape($t['nombre'] . ' ' . $t['codigo'] . ' ' . ($t['descripcion'] ?? ''))) ?>">
                    <div class="card h-100 shadow-sm border-0 portal-card d-flex flex-column">
                        <div class="card-body p-4 d-flex flex-column">
                            <!-- Cabecera de la Tarjeta -->
                            <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                <span class="badge bg-light text-secondary border font-monospace small px-2 py-1">
                                    <?= ViewHelper::escape($t['codigo']) ?>
                                </span>
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle small px-2 py-1">
                                    <?= ViewHelper::escape($t['categoria_nombre'] ?? 'General') ?>
                                </span>
                            </div>

                            <h5 class="fw-bold text-dark mb-2" style="font-size: 1.05rem; line-height: 1.35;">
                                <?= ViewHelper::escape($t['nombre']) ?>
                            </h5>

                            <?php if (!empty($t['descripcion'])): ?>
                                <p class="text-muted small mb-3">
                                    <?= ViewHelper::escape(ViewHelper::truncate($t['descripcion'], 120)) ?>
                                </p>
                            <?php endif; ?>

                            <!-- Requisitos Relevantes -->
                            <div class="p-3 bg-light rounded mb-3 flex-grow-1 border">
                                <div class="small fw-bold text-secondary mb-1">
                                    <i class="bi bi-card-checklist me-1 text-primary"></i> Requisitos solicitados:
                                </div>
                                <?php if (!empty($reqs)): ?>
                                    <ul class="list-unstyled mb-0 small text-muted">
                                        <?php foreach (array_slice($reqs, 0, 3) as $r): 
                                            $rClean = trim($r);
                                            if (empty($rClean)) continue;
                                        ?>
                                            <li class="mb-1 text-truncate" title="<?= ViewHelper::escape($rClean) ?>">
                                                <i class="bi bi-check2 text-success me-1"></i><?= ViewHelper::escape($rClean) ?>
                                            </li>
                                        <?php endforeach; ?>
                                        <?php if (count($reqs) > 3): ?>
                                            <li class="text-muted fst-italic" style="font-size: 0.75rem;">+ <?= count($reqs) - 3 ?> requisitos adicionales</li>
                                        <?php endif; ?>
                                    </ul>
                                <?php else: ?>
                                    <span class="small text-muted fst-italic">Presentar solicitud estándar mediante FUT.</span>
                                <?php endif; ?>
                            </div>

                            <!-- Metadatos de Plazo y Costo -->
                            <div class="d-flex justify-content-between align-items-center pt-2 border-top mb-3">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">PLAZO LEGAL</small>
                                    <span class="badge bg-light text-primary border fw-semibold">
                                        <i class="bi bi-clock-history me-1"></i><?= $plazo ?> días hábiles
                                    </span>
                                </div>
                                <div class="text-end">
                                    <small class="text-muted d-block" style="font-size: 0.72rem;">DERECHO DE TRÁMITE</small>
                                    <?php if ($costo > 0): ?>
                                        <span class="fw-bold text-success font-monospace fs-6">S/ <?= number_format($costo, 2) ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle fw-bold">Gratuito</span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Botón Iniciar Trámite -->
                            <a href="<?= ViewHelper::url('/tramite?procedimiento=' . $t['id']) ?>" class="btn btn-primary fw-bold w-100 py-2 shadow-xs">
                                <i class="bi bi-pencil-square me-1"></i> Iniciar este Trámite en FUT
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Mensaje cuando no hay resultados de búsqueda -->
    <div id="noResults" class="text-center py-5 d-none">
        <i class="bi bi-search fs-1 text-secondary opacity-50 d-block mb-3"></i>
        <h5 class="fw-bold text-dark">No se encontraron trámites</h5>
        <p class="text-muted small">Intente con otros términos de búsqueda o seleccione otra categoría.</p>
        <button type="button" id="btnResetFilters" class="btn btn-outline-primary btn-sm">Ver todos los trámites</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchTramite');
    const filterCat = document.getElementById('filterCategoria');
    const items = document.querySelectorAll('.tramite-item');
    const countBadge = document.getElementById('tramitesCount');
    const noResults = document.getElementById('noResults');
    const resetBtn = document.getElementById('btnResetFilters');

    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const cat = filterCat.value;
        let visible = 0;

        items.forEach(item => {
            const itemCat = item.dataset.cat;
            const itemText = item.dataset.search || '';

            const matchCat = (cat === 'todos' || itemCat === cat);
            const matchSearch = (query === '' || itemText.includes(query));

            if (matchCat && matchSearch) {
                item.classList.remove('d-none');
                visible++;
            } else {
                item.classList.add('d-none');
            }
        });

        countBadge.textContent = `Mostrando ${visible} procedimientos`;
        if (visible === 0) {
            noResults.classList.remove('d-none');
        } else {
            noResults.classList.add('d-none');
        }
    }

    searchInput.addEventListener('input', applyFilters);
    filterCat.addEventListener('change', applyFilters);

    resetBtn?.addEventListener('click', function() {
        searchInput.value = '';
        filterCat.value = 'todos';
        applyFilters();
    });
});
</script>
