<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Helpers\Csrf;

class CsrfMiddleware
{
    public function handle(Request $request, Response $response): void
    {
        if ($request->isPost()) {
            $token = $request->post('_csrf_token') ?? 
                     $request->get('_csrf_token') ?? 
                     ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);

            if (!Csrf::validate($token)) {
                $response->setStatusCode(419);
                if ($request->isAjax()) {
                    $response->json([
                        'success' => false,
                        'message' => 'La sesión del formulario expiró por inactividad. Recargue la página e intente nuevamente.'
                    ], 419);
                }
                echo "<h1>419 - Página expirada (Token CSRF no válido)</h1>";
                echo "<p>Por motivos de seguridad institucional, debe recargar la página para reintentar el envío.</p>";
                echo "<p><a href='javascript:history.back()'>Volver atrás</a></p>";
                exit;
            }
        }
    }
}
