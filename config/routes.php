<?php
declare(strict_types=1);

/**
 * Mesa de Partes Virtual - IESP Túpac Amaru Cusco
 * Definición de Rutas del Sistema
 * 
 * @var \App\Core\Router $router
 */

$router->get('/health', function($request, $response) {
    $response->json([
        'status' => 'ok',
        'sistema' => 'Mesa de Partes Virtual - IESP Túpac Amaru',
        'version' => '1.0.0',
        'php' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});
