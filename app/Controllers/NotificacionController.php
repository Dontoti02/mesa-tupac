<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Notificacion;

class NotificacionController extends Controller
{
    private Notificacion $notifModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->notifModel = new Notificacion();
    }

    public function index(): void
    {
        $user = $this->user();
        $userId = (int)$user['id'];
        $unidadId = !empty($user['unidad_id']) ? (int)$user['unidad_id'] : null;

        $notificaciones = $this->notifModel->getForUser($userId, $unidadId, 50);
        $unreadCount = $this->notifModel->getUnreadCount($userId, $unidadId);

        $this->view('notificaciones.index', [
            'title' => 'Centro de Notificaciones del Sistema',
            'notificaciones' => $notificaciones,
            'unreadCount' => $unreadCount
        ], 'app');
    }

    public function marcarLeidas(): void
    {
        $user = $this->user();
        $userId = (int)$user['id'];
        $unidadId = !empty($user['unidad_id']) ? (int)$user['unidad_id'] : null;

        $this->notifModel->markAllAsRead($userId, $unidadId);

        $this->redirect('/notificaciones', ['success' => 'Todas las notificaciones han sido marcadas como leídas.']);
    }

    public function unreadCount(): void
    {
        $user = $this->user();
        if (!$user) {
            $this->response->json(['unread' => 0]);
            return;
        }

        $userId = (int)$user['id'];
        $unidadId = !empty($user['unidad_id']) ? (int)$user['unidad_id'] : null;
        $count = $this->notifModel->getUnreadCount($userId, $unidadId);

        $this->response->json(['unread' => $count]);
    }
}
