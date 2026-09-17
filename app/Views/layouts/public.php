<?php
use App\Models\Configuracion;
use App\Helpers\ViewHelper;

$config = Configuracion::getAll();
$instNombre = $config['institucion_nombre'] ?? 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO';
$instNombreCorto = $config['institucion_nombre_corto'] ?? 'IESP Túpac Amaru';
$instDependencia = $config['institucion_dependencia'] ?? 'GERENCIA REGIONAL DE EDUCACIÓN CUSCO';
$instResolucion = $config['institucion_resolucion'] ?? 'R.M 195-2005-ED';
$instDireccion = $config['institucion_direccion'] ?? 'Av. Cusco N° 456, San Sebastián, Cusco';
$instTelefono = $config['institucion_telefono'] ?? '(084) 223344';
$instCorreo = $config['institucion_correo'] ?? 'mesadepartes@tupacamaru.edu.pe';
$logoPrincipal = !empty($config['logo_principal']) ? ViewHelper::url($config['logo_principal']) : '';
$favicon = !empty($config['favicon']) ? ViewHelper::url($config['favicon']) : '';

// Variables dinámicas
$colorPrimary = $config['color_primario'] ?? '#B3261E';
$colorPrimaryHover = $config['color_primario_oscuro'] ?? '#7F1D1D';
$colorPrimaryLight = $config['color_primario_suave'] ?? '#FEE2E2';
$colorSecondary = $config['color_secundario'] ?? '#F97316';
$colorSidebar = $config['color_sidebar'] ?? '#374151';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ViewHelper::escape($title ?? 'Portal de Mesa de Partes Virtual') ?> | <?= ViewHelper::escape($instNombreCorto) ?></title>
    
    <?php if (!empty($favicon)): ?>
        <link rel="shortcut icon" href="<?= $favicon ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos y Variables Dinámicas -->
    <link rel="stylesheet" href="<?= ViewHelper::asset('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= ViewHelper::asset('css/public.css') ?>">

    <style>
        :root {
            --color-primary: <?= ViewHelper::escape($colorPrimary) ?>;
            --color-primary-hover: <?= ViewHelper::escape($colorPrimaryHover) ?>;
            --color-primary-light: <?= ViewHelper::escape($colorPrimaryLight) ?>;
            --color-secondary: <?= ViewHelper::escape($colorSecondary) ?>;
            --color-sidebar: <?= ViewHelper::escape($colorSidebar) ?>;
        }
    </style>
</head>
<body>

    <!-- Barra de Navegación Pública -->
    <nav class="navbar navbar-expand-lg public-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand-inst" href="<?= ViewHelper::url('/') ?>">
                <?php if (!empty($logoPrincipal)): ?>
                    <img src="<?= $logoPrincipal ?>" alt="Logo" style="max-height: 45px;">
                <?php else: ?>
                    <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-bank fs-3" style="color: var(--color-primary);"></i>
                    </div>
                <?php endif; ?>
                <div>
                    <div class="title"><?= ViewHelper::escape($instNombreCorto) ?></div>
                    <div class="subtitle">Mesa de Partes Virtual Oficial &bull; <?= ViewHelper::escape($instResolucion) ?></div>
                </div>
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navPublicContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navPublicContent">
                <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2 mt-3 mt-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 <?= ViewHelper::isActive('/') ?>" href="<?= ViewHelper::url('/') ?>">
                            <i class="bi bi-house-door me-1"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 <?= ViewHelper::isActive('/tramite') ?>" href="<?= ViewHelper::url('/tramite') ?>">
                            <i class="bi bi-pencil-square me-1"></i> Presentar FUT
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold px-3 <?= ViewHelper::isActive('/consulta') ?>" href="<?= ViewHelper::url('/consulta') ?>">
                            <i class="bi bi-search me-1"></i> Consultar Trámite
                        </a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="btn btn-outline-secondary btn-sm px-3" href="<?= ViewHelper::url('/login') ?>">
                            <i class="bi bi-person-lock me-1"></i> Acceso Personal
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <main class="flex-grow-1">
        <div class="container pt-3">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>
        </div>
        <?= $content ?>
    </main>

    <!-- Footer Ciudadano -->
    <footer class="public-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-5">
                    <h6 class="text-white fw-bold mb-3"><?= ViewHelper::escape($instNombre) ?></h6>
                    <p class="small text-muted mb-2">Dependiente de la <?= ViewHelper::escape($instDependencia) ?>. Autorizado mediante <?= ViewHelper::escape($instResolucion) ?>.</p>
                    <p class="small text-muted">Mesa de Partes Virtual disponible para la recepción digital de solicitudes, trámites académicos, administrativos y consultas ciudadanas.</p>
                </div>
                <div class="col-lg-4">
                    <h6 class="text-white fw-bold mb-3">Atención y Contacto</h6>
                    <ul class="list-unstyled small text-muted mb-0 d-flex flex-column gap-2">
                        <li><i class="bi bi-geo-alt-fill text-warning me-2"></i><?= ViewHelper::escape($instDireccion) ?></li>
                        <li><i class="bi bi-telephone-fill text-warning me-2"></i><?= ViewHelper::escape($instTelefono) ?></li>
                        <li><i class="bi bi-envelope-fill text-warning me-2"></i><?= ViewHelper::escape($instCorreo) ?></li>
                        <li><i class="bi bi-clock-fill text-warning me-2"></i>Lunes a Viernes: 08:00 a 16:30 hrs.</li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Accesos Directos</h6>
                    <div class="d-flex flex-column gap-2 small">
                        <a href="<?= ViewHelper::url('/tramite') ?>" class="text-decoration-none text-light">
                            <i class="bi bi-chevron-right me-1 text-warning"></i> Formulario FUT Digital
                        </a>
                        <a href="<?= ViewHelper::url('/consulta') ?>" class="text-decoration-none text-light">
                            <i class="bi bi-chevron-right me-1 text-warning"></i> Seguimiento de Expedientes
                        </a>
                        <a href="<?= ViewHelper::url('/login') ?>" class="text-decoration-none text-light">
                            <i class="bi bi-chevron-right me-1 text-warning"></i> Intranet de Funcionarios
                        </a>
                    </div>
                </div>
            </div>
            <hr class="my-4 border-secondary opacity-25">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center small text-muted gap-2">
                <div>&copy; <?= date('Y') ?> <?= ViewHelper::escape($instNombreCorto) ?>. Todos los derechos reservados.</div>
                <div>Sistema Web de Trámite Documentario Digital &bull; Transparencia Institucional</div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
