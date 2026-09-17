<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
    protected Request $request;
    protected Response $response;

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    protected function view(string $view, array $data = [], string|bool|null $layout = 'app'): void
    {
        $layoutStr = ($layout === false || $layout === null) ? null : (string)$layout;
        // Pasar automáticamente el usuario autenticado a todas las vistas
        if (!isset($data['currentUser'])) {
            $data['currentUser'] = Session::get('user');
        }
        $this->response->view($view, $data, $layoutStr);
    }

    protected function json(mixed $data, int $statusCode = 200): void
    {
        $this->response->json($data, $statusCode);
    }

    protected function redirect(string $path, array $flash = []): void
    {
        $this->response->redirect($path, $flash);
    }

    protected function user(): ?array
    {
        return Session::get('user');
    }

    protected function userId(): ?int
    {
        $user = $this->user();
        return isset($user['id']) ? (int)$user['id'] : null;
    }
}
