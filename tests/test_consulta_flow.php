<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/consulta?expediente=EXP-2026-000001';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_GET['expediente'] = 'EXP-2026-000001';

$request = new \App\Core\Request();
$response = new \App\Core\Response();
$controller = new \App\Controllers\ConsultaController($request, $response);

echo "Probando consulta pública de EXP-2026-000001...\n";
ob_start();
$controller->index();
$output = ob_get_clean();

if (str_contains($output, 'EXP-2026-000001') && str_contains($output, 'Línea de Tiempo del Trámite')) {
    echo "CONSULTA PÚBLICA FUNCIONANDO CORRECTAMENTE CON LÍNEA DE TIEMPO.\n";
} else {
    die("ERROR: La consulta pública no retornó la información esperada.\n");
}
