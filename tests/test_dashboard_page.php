<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

// Simular sesión autenticada como admin
\App\Core\Session::set('user', [
    'id' => 1,
    'dni' => '00000001',
    'username' => 'admin',
    'nombres' => 'Administrador',
    'apellidos' => 'General',
    'nombre_completo' => 'Administrador General',
    'email' => 'admin@tupacamaru.edu.pe',
    'cargo' => 'Administrador del Sistema',
    'unidad_id' => null,
    'debe_cambiar_password' => 0,
    'roles' => ['superadmin'],
    'permissions' => []
]);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/dashboard';
$_SERVER['SCRIPT_NAME'] = '/index.php';

$request = new \App\Core\Request();
$response = new \App\Core\Response();
$controller = new \App\Controllers\DashboardController($request, $response);

echo "Probando ejecución de DashboardController::index()...\n";
ob_start();
$controller->index();
$output = ob_get_clean();

if (str_contains($output, 'Panel de Control Institucional') && str_contains($output, 'Trámites Hoy')) {
    echo "DASHBOARD RENDERIZADO EXITOSAMENTE CON LAYOUT Y VARIABLES CSS.\n";
} else {
    echo "ERROR: El dashboard no contiene el texto esperado.\n";
}
