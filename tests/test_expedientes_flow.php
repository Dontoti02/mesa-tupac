<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

\App\Core\Session::set('user', [
    'id' => 1,
    'dni' => '00000001',
    'username' => 'admin',
    'nombres' => 'Administrador',
    'apellidos' => 'General',
    'nombre_completo' => 'Administrador General',
    'email' => 'admin@tupacamaru.edu.pe',
    'cargo' => 'Administrador',
    'unidad_id' => null,
    'debe_cambiar_password' => 0,
    'roles' => ['superadmin'],
    'permissions' => []
]);

echo "1. Probando renderizado de listado general /expedientes...\n";
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/expedientes';
$_SERVER['SCRIPT_NAME'] = '/index.php';

$request = new \App\Core\Request();
$response = new \App\Core\Response();
$controller = new \App\Controllers\ExpedienteController($request, $response);

ob_start();
$controller->index();
$outputList = ob_get_clean();

if (str_contains($outputList, 'Registro Maestro de Expedientes') && str_contains($outputList, 'EXP-2026-000001')) {
    echo "LISTADO MAESTRO RENDERIZADO CORRECTAMENTE.\n";
} else {
    die("ERROR: Listado maestro no contiene la información esperada.\n");
}

echo "2. Probando vista 360 de /expedientes/1...\n";
ob_start();
$controller->show($request, $response, '1');
$outputShow = ob_get_clean();

if (str_contains($outputShow, 'EXP-2026-000001') && str_contains($outputShow, 'Bitácora Histórica de Trazabilidad')) {
    echo "DETALLE 360 DEL EXPEDIENTE Y TRAZABILIDAD RENDERIZADO CORRECTAMENTE.\n";
} else {
    die("ERROR: Vista de detalle 360 no contiene la información esperada.\n");
}

echo "TODAS LAS PRUEBAS DE EXPEDIENTES PASARON CON ÉXITO.\n";
