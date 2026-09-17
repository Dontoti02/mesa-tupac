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
use App\Controllers\AdministracionController;
use App\Controllers\ConfiguracionController;

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

// Administración del Sistema (Superadmin y Admin)
$router->get('/administracion/usuarios', [AdministracionController::class, 'usuarios'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/usuarios', [AdministracionController::class, 'storeUsuario'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);
$router->post('/administracion/usuarios/{id}', [AdministracionController::class, 'updateUsuario'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

$router->get('/administracion/roles', [AdministracionController::class, 'roles'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/roles/{id}/permisos', [AdministracionController::class, 'syncRolPermisos'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

$router->get('/administracion/unidades', [AdministracionController::class, 'unidades'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/unidades', [AdministracionController::class, 'storeUnidad'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);
$router->post('/administracion/unidades/{id}', [AdministracionController::class, 'updateUnidad'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

$router->get('/administracion/programas', [AdministracionController::class, 'programas'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/programas', [AdministracionController::class, 'storePrograma'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);
$router->post('/administracion/programas/{id}', [AdministracionController::class, 'updatePrograma'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

$router->get('/administracion/tramites', [AdministracionController::class, 'tramites'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/tramites', [AdministracionController::class, 'storeTramite'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);
$router->post('/administracion/tramites/{id}', [AdministracionController::class, 'updateTramite'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

$router->get('/administracion/estados', [AdministracionController::class, 'estados'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/estados/{id}', [AdministracionController::class, 'updateEstado'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

// Configuración Institucional, Apariencia y SMTP
$router->get('/administracion/configuracion', [ConfiguracionController::class, 'configuracion'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/configuracion', [ConfiguracionController::class, 'updateConfiguracion'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

$router->get('/administracion/apariencia', [ConfiguracionController::class, 'apariencia'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/apariencia', [ConfiguracionController::class, 'updateApariencia'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);

$router->get('/administracion/smtp', [ConfiguracionController::class, 'smtp'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin']);
$router->post('/administracion/smtp', [ConfiguracionController::class, 'updateSmtp'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);
$router->post('/administracion/smtp/probar', [ConfiguracionController::class, 'probarSmtp'], ['AuthMiddleware', 'RoleMiddleware:superadmin|admin', 'CsrfMiddleware']);
