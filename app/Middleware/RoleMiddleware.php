<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Usuario;

class RoleMiddleware
{
    public function handle(Request $request, Response $response, string $roles = ''): void
    {
        $user = Session::get('user');

        if (!$user) {
            $response->redirect('/login');
        }

        if (empty($roles)) {
            return;
        }

        $allowedRoles = explode('|', $roles);
        $usuarioModel = new Usuario();

        // Superadmin siempre pasa
        if ($usuarioModel->hasRole((int)$user['id'], 'superadmin')) {
            return;
        }

        $hasAccess = false;
        foreach ($allowedRoles as $role) {
            if ($usuarioModel->hasRole((int)$user['id'], trim($role))) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $response->setStatusCode(403);
            if ($request->isAjax()) {
                $response->json([
                    'success' => false,
                    'message' => 'Acceso denegado: Su rol no cuenta con privilegios para esta sección.'
                ], 403);
            }
            $response->view('errors.403', [
                'title' => 'Acceso Denegado',
                'permission' => 'Rol requerido: ' . $roles
            ], 'app');
        }
    }
}
