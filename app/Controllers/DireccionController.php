<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Request;
use App\Core\Response;
use App\Models\Expediente;
use App\Models\ExpedienteDocumento;
use App\Models\ExpedienteMovimiento;
use App\Models\Unidad;
use App\Models\Prioridad;
use App\Models\EstadoExpediente;
use App\Models\Notificacion;
use App\Models\Auditoria;
use App\Helpers\Validator;

class DireccionController extends Controller
{
    private Expediente $expedienteModel;
    private ExpedienteMovimiento $movimientoModel;
    private ExpedienteDocumento $documentoModel;
    private Unidad $unidadModel;
    private Prioridad $prioridadModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->expedienteModel = new Expediente();
        $this->movimientoModel = new ExpedienteMovimiento();
        $this->documentoModel = new ExpedienteDocumento();
        $this->unidadModel = new Unidad();
        $this->prioridadModel = new Prioridad();
    }

    public function index(): void
    {
        $tab = $this->request->get('tab', 'por_derivar');
        $search = trim((string)$this->request->get('q', ''));

        $sql = "SELECT e.*, 
                       es.nombre as estado_nombre, es.color as estado_color, es.codigo as estado_codigo,
                       p.nombre as prioridad_nombre, p.color as prioridad_color,
                       t.nombre as tramite_nombre,
                       u.nombre as unidad_nombre 
                FROM `expedientes` e 
                LEFT JOIN `estados_expediente` es ON e.estado_id = es.id 
                LEFT JOIN `prioridades` p ON e.prioridad_id = p.id 
                LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id 
                LEFT JOIN `unidades` u ON e.unidad_actual_id = u.id 
                WHERE e.activo = 1";
        
        $params = [];

        switch ($tab) {
            case 'por_derivar':
                $sql .= " AND es.codigo IN ('ENVIADO_A_DIRECCION', 'EN_REVISION')";
                break;
            case 'derivados':
                $sql .= " AND es.codigo IN ('DERIVADO', 'RECEPCIONADO', 'EN_TRAMITE')";
                break;
            case 'respuestas':
                $sql .= " AND es.codigo IN ('RESPONDIDO', 'EN_REVISION_DIRECCION')";
                break;
            case 'observados':
                $sql .= " AND es.codigo IN ('OBSERVADO', 'DEVUELTO')";
                break;
            case 'finalizados':
                $sql .= " AND es.codigo IN ('FINALIZADO', 'APROBADO', 'ARCHIVADO')";
                break;
            case 'todos':
            default:
                break;
        }

        if (!empty($search)) {
            $sql .= " AND (e.numero_expediente LIKE :search OR e.codigo_seguimiento LIKE :search OR e.dni LIKE :search OR e.nombres LIKE :search OR e.apellido_paterno LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY e.fecha_ingreso DESC";

        $stmt = $this->expedienteModel->db()->prepare($sql);
        $stmt->execute($params);
        $expedientes = $stmt->fetchAll();

        // Conteos por estado
        $db = Database::getConnection();
        $cPorDerivar = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('ENVIADO_A_DIRECCION', 'EN_REVISION') AND e.activo = 1")->fetch()['t'];
        $cDerivados = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('DERIVADO', 'RECEPCIONADO', 'EN_TRAMITE') AND e.activo = 1")->fetch()['t'];
        $cRespuestas = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('RESPONDIDO', 'EN_REVISION_DIRECCION') AND e.activo = 1")->fetch()['t'];
        $cObservados = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('OBSERVADO', 'DEVUELTO') AND e.activo = 1")->fetch()['t'];
        $cFinalizados = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('FINALIZADO', 'APROBADO', 'ARCHIVADO') AND e.activo = 1")->fetch()['t'];

        $unidades = $this->unidadModel->allActive();
        $prioridades = $this->prioridadModel->all('orden ASC');

        $this->view('direccion.index', [
            'title' => 'Bandeja de Dirección General',
            'expedientes' => $expedientes,
            'currentTab' => $tab,
            'search' => $search,
            'unidades' => $unidades,
            'prioridades' => $prioridades,
            'counts' => [
                'por_derivar' => $cPorDerivar,
                'derivados' => $cDerivados,
                'respuestas' => $cRespuestas,
                'observados' => $cObservados,
                'finalizados' => $cFinalizados
            ]
        ], 'app');
    }

    public function derivar(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $user = $this->user();
        $unidadDestinoId = (int)$this->request->post('unidad_destino_id');
        $prioridadId = (int)$this->request->post('prioridad_id');
        $instruccion = trim((string)$this->request->post('instruccion', 'Derivado para atención y emisión de informe correspondiente.'));

        $validator = Validator::make([
            'unidad_destino_id' => $unidadDestinoId,
            'prioridad_id' => $prioridadId,
            'instruccion' => $instruccion
        ], [
            'unidad_destino_id' => 'required|numeric',
            'prioridad_id' => 'required|numeric',
            'instruccion' => 'required'
        ]);

        if ($validator->fails()) {
            $this->redirect('/direccion', [
                'error' => $validator->firstOfAll()
            ]);
        }

        $exp = $this->expedienteModel->find($expedienteId);
        if (!$exp) {
            $this->redirect('/direccion', ['error' => 'Expediente no encontrado.']);
        }

        try {
            Database::transaction(function($pdo) use ($exp, $expedienteId, $user, $unidadDestinoId, $prioridadId, $instruccion) {
                $estadoModel = new EstadoExpediente();
                $nuevoEstado = $estadoModel->findByCodigo('DERIVADO');
                $nuevoEstadoId = $nuevoEstado ? (int)$nuevoEstado['id'] : 5;

                // 1. Actualizar expediente: estado DERIVADO, unidad actual y prioridad
                $this->expedienteModel->update($expedienteId, [
                    'estado_id' => $nuevoEstadoId,
                    'unidad_actual_id' => $unidadDestinoId,
                    'unidad_responsable_id' => $unidadDestinoId,
                    'prioridad_id' => $prioridadId
                ]);

                // 2. Insertar movimiento de derivación
                $this->movimientoModel->record(
                    $expedienteId,
                    'DERIVACION',
                    1, // Dirección General
                    $unidadDestinoId,
                    (int)$user['id'],
                    (int)$exp['estado_id'],
                    $nuevoEstadoId,
                    $instruccion,
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                // 3. Notificación a la Unidad Orgánica destino
                Notificacion::send(
                    null,
                    $unidadDestinoId,
                    "Expediente Derivado por Dirección: {$exp['numero_expediente']}",
                    "Dirección General le ha asignado el expediente para su atención e informe técnico.",
                    "/mi-unidad",
                    'warning'
                );

                // 4. Auditoría
                Auditoria::log(
                    (int)$user['id'],
                    'DERIVACION_EXPEDIENTE',
                    'direccion',
                    $expedienteId,
                    ['estado_id' => $exp['estado_id'], 'unidad_actual_id' => $exp['unidad_actual_id']],
                    ['estado_id' => $nuevoEstadoId, 'unidad_destino_id' => $unidadDestinoId, 'instruccion' => $instruccion]
                );
            });

            $this->redirect('/direccion?tab=derivados', [
                'success' => "Expediente {$exp['numero_expediente']} derivado exitosamente a la unidad orgánica correspondiente."
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/direccion', [
                'error' => 'Error al derivar expediente: ' . $e->getMessage()
            ]);
        }
    }

    public function observar(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $user = $this->user();
        $observacion = trim((string)$this->request->post('observacion', ''));

        if (empty($observacion)) {
            $this->redirect('/direccion', ['error' => 'Debe indicar el motivo o causal de la observación.']);
        }

        $exp = $this->expedienteModel->find($expedienteId);
        if (!$exp) {
            $this->redirect('/direccion', ['error' => 'Expediente no encontrado.']);
        }

        try {
            Database::transaction(function($pdo) use ($exp, $expedienteId, $user, $observacion) {
                $estadoModel = new EstadoExpediente();
                $nuevoEstado = $estadoModel->findByCodigo('OBSERVADO');
                $nuevoEstadoId = $nuevoEstado ? (int)$nuevoEstado['id'] : 9;

                $this->expedienteModel->update($expedienteId, [
                    'estado_id' => $nuevoEstadoId,
                    'observacion_publica' => $observacion
                ]);

                $this->movimientoModel->record(
                    $expedienteId,
                    'OBSERVACION',
                    $exp['unidad_actual_id'],
                    $exp['unidad_actual_id'],
                    (int)$user['id'],
                    (int)$exp['estado_id'],
                    $nuevoEstadoId,
                    $observacion,
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                Auditoria::log((int)$user['id'], 'OBSERVAR_EXPEDIENTE', 'direccion', $expedienteId, null, [
                    'observacion' => $observacion
                ]);
            });

            $this->redirect('/direccion?tab=observados', [
                'warning' => "Expediente {$exp['numero_expediente']} marcado como OBSERVADO."
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/direccion', ['error' => 'Error al observar expediente: ' . $e->getMessage()]);
        }
    }

    public function aprobar(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $user = $this->user();
        $observacion = trim((string)$this->request->post('observacion', 'Respuesta técnica evaluada y aprobada en su totalidad por Dirección General. Trámite finalizado.'));

        $exp = $this->expedienteModel->find($expedienteId);
        if (!$exp) {
            $this->redirect('/direccion', ['error' => 'Expediente no encontrado.']);
        }

        try {
            Database::transaction(function($pdo) use ($exp, $expedienteId, $user, $observacion) {
                $estadoModel = new EstadoExpediente();
                $nuevoEstado = $estadoModel->findByCodigo('FINALIZADO');
                $nuevoEstadoId = $nuevoEstado ? (int)$nuevoEstado['id'] : 14;

                $this->expedienteModel->update($expedienteId, [
                    'estado_id' => $nuevoEstadoId,
                    'fecha_finalizacion' => date('Y-m-d H:i:s'),
                    'observacion_publica' => $observacion
                ]);

                $this->movimientoModel->record(
                    $expedienteId,
                    'FINALIZACION',
                    1, // Dirección
                    1, // Mesa de Partes
                    (int)$user['id'],
                    (int)$exp['estado_id'],
                    $nuevoEstadoId,
                    $observacion,
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                Notificacion::send(
                    null,
                    1, // Mesa de partes
                    "Trámite Aprobado y Finalizado: {$exp['numero_expediente']}",
                    "Dirección General ha aprobado el expediente para entrega de resolución/resultado al solicitante.",
                    "/expedientes/{$expedienteId}",
                    'success'
                );

                Auditoria::log((int)$user['id'], 'APROBAR_Y_FINALIZAR', 'direccion', $expedienteId);
            });

            $this->redirect('/direccion?tab=finalizados', [
                'success' => "Expediente {$exp['numero_expediente']} aprobado y finalizado oficialmente."
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/direccion', ['error' => 'Error al aprobar y finalizar expediente: ' . $e->getMessage()]);
        }
    }
}
