<?php
use App\Models\Configuracion;
use App\Helpers\ViewHelper;

$config = Configuracion::getAll();
$instNombre = $config['institucion_nombre'] ?? 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO';
$instNombreCorto = $config['institucion_nombre_corto'] ?? 'IESP Túpac Amaru';
$logoLogin = !empty($config['logo_login']) ? ViewHelper::url($config['logo_login']) : '';
$favicon = !empty($config['favicon']) ? ViewHelper::url($config['favicon']) : '';

// Colores institucionales dinámicos
$colorPrimary = $config['color_primario'] ?? '#B3261E';
$colorPrimaryHover = $config['color_primario_oscuro'] ?? '#7F1D1D';
$colorSecondary = $config['color_secundario'] ?? '#F97316';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ViewHelper::escape($title ?? 'Acceso Institucional') ?> | <?= ViewHelper::escape($instNombreCorto) ?></title>
    <?php if (!empty($favicon)): ?>
        <link rel="shortcut icon" href="<?= $favicon ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos Institucionales y Variables CSS Dinámicas -->
    <link rel="stylesheet" href="<?= ViewHelper::asset('css/variables.css') ?>">
    <style>
        :root {
            --color-primary: <?= ViewHelper::escape($colorPrimary) ?>;
            --color-primary-hover: <?= ViewHelper::escape($colorPrimaryHover) ?>;
            --color-secondary: <?= ViewHelper::escape($colorSecondary) ?>;
        }

        body {
            background-color: var(--color-bg);
            font-family: var(--font-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
            color: var(--color-dark);
        }

        .auth-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--color-border);
            overflow: hidden;
        }

        .auth-header {
            background: linear-gradient(135deg, var(--color-primary) 0%, var(--color-primary-hover) 100%);
            color: #ffffff;
            padding: 2.25rem 1.75rem 1.75rem;
            text-align: center;
            border-bottom: 4px solid var(--color-secondary);
        }

        .auth-body {
            padding: 2rem;
        }

        .btn-primary {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            font-weight: 600;
            padding: 0.75rem 1.25rem;
            border-radius: var(--radius-sm);
            transition: all 0.2s ease-in-out;
        }

        .btn-primary:hover, .btn-primary:focus {
            background-color: var(--color-primary-hover);
            border-color: var(--color-primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }

        .form-control:focus {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 0.25rem rgba(179, 38, 30, 0.15);
        }

        .auth-footer-text {
            color: var(--color-muted);
            font-size: 0.825rem;
            text-align: center;
            margin-top: 1.75rem;
        }
    </style>
</head>
<body>

    <div class="auth-card">
        <div class="auth-header">
            <?php if (!empty($logoLogin)): ?>
                <img src="<?= $logoLogin ?>" alt="Logo" class="mb-3" style="max-height: 70px;">
            <?php else: ?>
                <div class="d-inline-flex align-items-center justify-content-center bg-white text-primary rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="bi bi-bank fs-2 text-danger"></i>
                </div>
            <?php endif; ?>
            <h5 class="fw-bold mb-1 text-white text-uppercase" style="letter-spacing: 0.5px; font-size: 1.05rem;">Mesa de Partes Virtual</h5>
            <div class="small text-white-50"><?= ViewHelper::escape($instNombreCorto) ?></div>
        </div>

        <div class="auth-body">
            <?php require __DIR__ . '/../partials/alerts.php'; ?>
            <?= $content ?>
        </div>
    </div>

    <div class="auth-footer-text">
        <div><strong><?= ViewHelper::escape($instNombre) ?></strong></div>
        <div class="mt-1">Sistema Web de Trámite Documentario Digital &copy; <?= date('Y') ?></div>
        <div class="mt-2">
            <a href="<?= ViewHelper::url('/') ?>" class="text-decoration-none text-muted">
                <i class="bi bi-arrow-left me-1"></i>Ir al Portal Público
            </a>
            <span class="mx-2">|</span>
            <a href="<?= ViewHelper::url('/consulta') ?>" class="text-decoration-none text-muted">
                <i class="bi bi-search me-1"></i>Consultar Expediente
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
