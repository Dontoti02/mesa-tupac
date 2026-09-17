<?php
declare(strict_types=1);

/**
 * Mesa de Partes Virtual - IESP Túpac Amaru Cusco
 * Definición de Rutas del Sistema
 * 
 * @var \App\Core\Router $router
 */

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\TramiteController;

// Health Check
$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'ok',
        'sistema' => 'Mesa de Partes Virtual - IESP Túpac Amaru',
        'version' => '1.0.0',
        'php' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Portal Ciudadano y FUT Digital
$router->get('/', [TramiteController::class, 'inicio']);
$router->get('/tramite', [TramiteController::class, 'showFut']);
$router->post('/tramite', [TramiteController::class, 'processFut'], ['CsrfMiddleware']);
$router->get('/tramite/confirmacion/{id}', [TramiteController::class, 'showConfirmacion']);
$router->get('/cargo/{id}', [TramiteController::class, 'showCargo']);

// Autenticación
$router->get('/login', [AuthController::class, 'showLogin'], ['GuestMiddleware']);
$router->post('/login', [AuthController::class, 'login'], ['CsrfMiddleware']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout'], ['CsrfMiddleware']);
$router->get('/cambiar-password', [AuthController::class, 'showChangePassword']);
$router->post('/cambiar-password', [AuthController::class, 'updatePassword'], ['CsrfMiddleware']);

// Panel Administrativo y Dashboard
$router->get('/dashboard', [DashboardController::class, 'index'], ['AuthMiddleware']);
