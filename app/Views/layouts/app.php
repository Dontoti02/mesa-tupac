<?php
use App\Models\Configuracion;
use App\Helpers\ViewHelper;

$config = Configuracion::getAll();
$instNombre = $config['institucion_nombre'] ?? 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO';
$instNombreCorto = $config['institucion_nombre_corto'] ?? 'IESP Túpac Amaru';
$favicon = !empty($config['favicon']) ? ViewHelper::url($config['favicon']) : '';

// Variables dinámicas de apariencia de la BD
$colorPrimary = $config['color_primario'] ?? '#B3261E';
$colorPrimaryHover = $config['color_primario_oscuro'] ?? '#7F1D1D';
$colorPrimaryLight = $config['color_primario_suave'] ?? '#FEE2E2';
$colorSecondary = $config['color_secundario'] ?? '#F97316';
$colorSecondaryHover = $config['color_secundario_oscuro'] ?? '#C2410C';
$colorSecondaryLight = $config['color_secundario_suave'] ?? '#FFEDD5';
$colorSidebar = $config['color_sidebar'] ?? '#374151';
$colorHeader = $config['color_header'] ?? '#FFFFFF';
$colorBg = $config['color_fondo'] ?? '#F3F4F6';
$colorDark = $config['color_texto_principal'] ?? '#374151';
$colorMuted = $config['color_texto_secundario'] ?? '#6B7280';
$colorBorder = $config['color_borde'] ?? '#D1D5DB';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ViewHelper::escape($title ?? 'Panel Institucional') ?> | <?= ViewHelper::escape($instNombreCorto) ?></title>
    
    <?php if (!empty($favicon)): ?>
        <link rel="shortcut icon" href="<?= $favicon ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- Bootstrap 5 CSS & Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Estilos Base y Variables Dinámicas -->
    <link rel="stylesheet" href="<?= ViewHelper::asset('css/variables.css') ?>">
    <link rel="stylesheet" href="<?= ViewHelper::asset('css/app.css') ?>">

    <!-- Variables CSS Institucionales Dinámicas Inyectadas -->
    <style id="dynamic-theme-vars">
        :root {
            --color-primary: <?= ViewHelper::escape($colorPrimary) ?>;
            --color-primary-hover: <?= ViewHelper::escape($colorPrimaryHover) ?>;
            --color-primary-light: <?= ViewHelper::escape($colorPrimaryLight) ?>;
            --color-secondary: <?= ViewHelper::escape($colorSecondary) ?>;
            --color-secondary-hover: <?= ViewHelper::escape($colorSecondaryHover) ?>;
            --color-secondary-light: <?= ViewHelper::escape($colorSecondaryLight) ?>;
            --color-sidebar: <?= ViewHelper::escape($colorSidebar) ?>;
            --color-header: <?= ViewHelper::escape($colorHeader) ?>;
            --color-bg: <?= ViewHelper::escape($colorBg) ?>;
            --color-dark: <?= ViewHelper::escape($colorDark) ?>;
            --color-muted: <?= ViewHelper::escape($colorMuted) ?>;
            --color-border: <?= ViewHelper::escape($colorBorder) ?>;
        }
    </style>
</head>
<body>

    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php require __DIR__ . '/../partials/sidebar.php'; ?>

        <!-- Main Panel -->
        <div class="admin-main">
            <!-- Header -->
            <?php require __DIR__ . '/../partials/header.php'; ?>

            <!-- Content -->
            <main class="admin-content">
                <?php require __DIR__ . '/../partials/alerts.php'; ?>
                <?= $content ?>
            </main>

            <!-- Footer -->
            <?php require __DIR__ . '/../partials/footer.php'; ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?= ViewHelper::asset('js/app.js') ?>"></script>
</body>
</html>
