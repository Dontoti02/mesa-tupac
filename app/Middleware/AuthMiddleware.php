<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;

class AuthMiddleware
{
    public function handle(Request $request, Response $response): void
    {
        $user = Session::get('user');

        if (!$user) {
            $response->redirect('/login', [
                'error' => 'Debe iniciar sesión para acceder al panel administrativo.'
            ]);
        }

        // Si el usuario tiene la marca de cambio obligatorio de contraseña
        $currentUri = $request->getUri();
        if (!empty($user['debe_cambiar_password']) && 
            $currentUri !== '/cambiar-password' && 
            $currentUri !== '/logout') {
            $response->redirect('/cambiar-password', [
                'warning' => 'Por razones de seguridad institucional, debe actualizar su contraseña antes de continuar.'
            ]);
        }
    }
}
