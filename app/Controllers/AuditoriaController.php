<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Auditoria;
use App\Models\Usuario;

class AuditoriaController extends Controller
{
    private Auditoria $auditoriaModel;
    private Usuario $usuarioModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->auditoriaModel = new Auditoria();
        $this->usuarioModel = new Usuario();
    }

    public function index(): void
    {
        $filters = [
            'modulo' => $this->request->get('modulo', ''),
            'accion' => $this->request->get('accion', ''),
            'usuario_id' => $this->request->get('usuario_id', ''),
            'fecha_desde' => $this->request->get('fecha_desde', date('Y-m-01')),
            'fecha_hasta' => $this->request->get('fecha_hasta', date('Y-m-d'))
        ];

        $logs = $this->auditoriaModel->getLatest(150, $filters);
        $usuarios = $this->usuarioModel->all('nombres ASC');

        $this->view('auditoria.index', [
            'title' => 'Auditoría, Trazabilidad y Seguridad del Sistema',
            'logs' => $logs,
            'usuarios' => $usuarios,
            'filters' => $filters
        ], 'app');
    }

    public function detalle(Request $request, Response $response, string $id): void
    {
        $db = Database::getConnection();
        $stmt = $db->prepare(
            "SELECT a.*, u.username, u.nombres, u.apellidos, u.cargo 
             FROM `auditoria` a 
             LEFT JOIN `usuarios` u ON a.usuario_id = u.id 
             WHERE a.id = :id"
        );
        $stmt->execute(['id' => (int)$id]);
        $log = $stmt->fetch();

        if (!$log) {
            $this->response->status(404)->json(['error' => 'Registro de auditoría no encontrado.']);
            return;
        }

        $log['datos_anteriores_parsed'] = $log['datos_anteriores'] ? json_decode($log['datos_anteriores'], true) : null;
        $log['datos_nuevos_parsed'] = $log['datos_nuevos'] ? json_decode($log['datos_nuevos'], true) : null;

        $this->response->json($log);
    }
}
