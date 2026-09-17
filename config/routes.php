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
use App\Controllers\ConsultaController;
use App\Controllers\MesaPartesController;
use App\Controllers\DireccionController;
use App\Controllers\UnidadController;
use App\Controllers\ExpedienteController;

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

// Consulta Pública y Seguimiento
$router->get('/consulta', [ConsultaController::class, 'index']);
$router->post('/consulta', [ConsultaController::class, 'index']);

// Autenticación
$router->get('/login', [AuthController::class, 'showLogin'], ['GuestMiddleware']);
$router->post('/login', [AuthController::class, 'login'], ['CsrfMiddleware']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->post('/logout', [AuthController::class, 'logout'], ['CsrfMiddleware']);
$router->get('/cambiar-password', [AuthController::class, 'showChangePassword']);
$router->post('/cambiar-password', [AuthController::class, 'updatePassword'], ['CsrfMiddleware']);

// Panel Administrativo y Dashboard
$router->get('/dashboard', [DashboardController::class, 'index'], ['AuthMiddleware']);

// Mesa de Partes
$router->get('/mesa-partes', [MesaPartesController::class, 'index'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|mesa_partes']);
$router->get('/mesa-partes/registrar', [MesaPartesController::class, 'showRegistrar'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|mesa_partes']);
$router->post('/mesa-partes/registrar', [MesaPartesController::class, 'storeRegistrar'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|mesa_partes', 'CsrfMiddleware']);
$router->post('/mesa-partes/{id}/enviar-direccion', [MesaPartesController::class, 'enviarDireccion'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|mesa_partes', 'CsrfMiddleware']);

// Dirección General
$router->get('/direccion', [DireccionController::class, 'index'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|direccion']);
$router->post('/direccion/{id}/derivar', [DireccionController::class, 'derivar'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|direccion', 'CsrfMiddleware']);
$router->post('/direccion/{id}/observar', [DireccionController::class, 'observar'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|direccion', 'CsrfMiddleware']);
$router->post('/direccion/{id}/aprobar', [DireccionController::class, 'aprobar'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin|direccion', 'CsrfMiddleware']);

// Bandeja de Unidad Orgánica ("Mi Unidad")
$router->get('/mi-unidad', [UnidadController::class, 'index'], ['AuthMiddleware']);
$router->post('/mi-unidad/{id}/recepcionar', [UnidadController::class, 'recepcionar'], ['AuthMiddleware', 'CsrfMiddleware']);
$router->get('/mi-unidad/{id}/responder', [UnidadController::class, 'showResponder'], ['AuthMiddleware']);
$router->post('/mi-unidad/{id}/responder', [UnidadController::class, 'storeResponder'], ['AuthMiddleware', 'CsrfMiddleware']);

// Expedientes Global, Detalle 360 y Descargas
$router->get('/expedientes', [ExpedienteController::class, 'index'], ['AuthMiddleware']);
$router->get('/expedientes/{id}', [ExpedienteController::class, 'show'], ['AuthMiddleware']);
$router->get('/expedientes/{id}/documento/{doc_id}', [ExpedienteController::class, 'descargarDocumento'], ['AuthMiddleware']);
