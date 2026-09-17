<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Usuario;

class PermissionMiddleware
{
    public function handle(Request $request, Response $response, string $permission = ''): void
    {
        $user = Session::get('user');

        if (!$user) {
            $response->redirect('/login');
        }

        if (empty($permission)) {
            return;
        }

        $usuarioModel = new Usuario();
        if (!$usuarioModel->hasPermission((int)$user['id'], $permission)) {
            $response->setStatusCode(403);
            if ($request->isAjax()) {
                $response->json([
                    'success' => false,
                    'message' => 'Acceso denegado: No cuenta con el permiso institucional requerido (' . $permission . ').'
                ], 403);
            }
            $response->view('errors.403', [
                'title' => 'Acceso Denegado',
                'permission' => $permission
            ], 'app');
        }
    }
}
