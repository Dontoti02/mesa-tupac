<?php
use App\Models\Configuracion;
use App\Helpers\ViewHelper;

$config = Configuracion::getAll();
$instNombre = $config['institucion_nombre'] ?? 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO';
$piePagina = $config['institucion_pie_pagina'] ?? 'Mesa de Partes Virtual Oficial. Atención de Lunes a Viernes de 08:00 a 16:30 hrs.';
?>

<footer class="bg-white border-top py-3 px-4 text-center text-md-start small text-muted mt-auto">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
        <div>
            <strong><?= ViewHelper::escape($instNombre) ?></strong> &bull; <?= ViewHelper::escape($piePagina) ?>
        </div>
        <div>
            Mesa de Partes Digital &copy; <?= date('Y') ?>
        </div>
    </div>
</footer>
