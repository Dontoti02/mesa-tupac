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
use App\Models\EstadoExpediente;
use App\Models\Notificacion;
use App\Models\Auditoria;
use App\Helpers\Validator;
use App\Helpers\FileUploader;

class UnidadController extends Controller
{
    private Expediente $expedienteModel;
    private ExpedienteDocumento $documentoModel;
    private ExpedienteMovimiento $movimientoModel;
    private Unidad $unidadModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->expedienteModel = new Expediente();
        $this->documentoModel = new ExpedienteDocumento();
        $this->movimientoModel = new ExpedienteMovimiento();
        $this->unidadModel = new Unidad();
    }

    public function index(): void
    {
        $user = $this->user();
        $unidadId = $user['unidad_id'] ?? null;

        if (!$unidadId) {
            $this->redirect('/dashboard', [
                'warning' => 'Su cuenta de usuario no está asignada a una unidad orgánica específica.'
            ]);
        }

        $tab = $this->request->get('tab', 'por_recepcionar');
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
                WHERE e.activo = 1 AND (e.unidad_actual_id = :uid OR e.unidad_responsable_id = :uid2)";
        
        $params = [
            'uid' => $unidadId,
            'uid2' => $unidadId
        ];

        switch ($tab) {
            case 'por_recepcionar':
                $sql .= " AND es.codigo = 'DERIVADO'";
                break;
            case 'en_tramite':
                $sql .= " AND es.codigo IN ('RECEPCIONADO', 'EN_TRAMITE')";
                break;
            case 'respondidos':
                $sql .= " AND es.codigo IN ('RESPONDIDO', 'EN_REVISION_DIRECCION', 'APROBADO', 'FINALIZADO')";
                break;
            case 'todos':
            default:
                break;
        }

        if (!empty($search)) {
            $sql .= " AND (e.numero_expediente LIKE :search OR e.dni LIKE :search OR e.nombres LIKE :search OR e.apellido_paterno LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $sql .= " ORDER BY e.fecha_ingreso DESC";

        $stmt = $this->expedienteModel->db()->prepare($sql);
        $stmt->execute($params);
        $expedientes = $stmt->fetchAll();

        // Conteos por estado de la unidad
        $db = Database::getConnection();
        $cPorRec = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo = 'DERIVADO' AND e.unidad_actual_id = {$unidadId} AND e.activo = 1")->fetch()['t'];
        $cEnTram = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('RECEPCIONADO', 'EN_TRAMITE') AND e.unidad_actual_id = {$unidadId} AND e.activo = 1")->fetch()['t'];
        $cResp = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('RESPONDIDO', 'FINALIZADO') AND (e.unidad_actual_id = {$unidadId} OR e.unidad_responsable_id = {$unidadId}) AND e.activo = 1")->fetch()['t'];

        $unidad = $this->unidadModel->find($unidadId);

        $this->view('unidad.index', [
            'title' => 'Bandeja de Unidad: ' . ($unidad['nombre'] ?? 'Mi Unidad'),
            'unidad' => $unidad,
            'expedientes' => $expedientes,
            'currentTab' => $tab,
            'search' => $search,
            'counts' => [
                'por_recepcionar' => $cPorRec,
                'en_tramite' => $cEnTram,
                'respondidos' => $cResp
            ]
        ], 'app');
    }

    public function recepcionar(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $user = $this->user();
        $unidadId = $user['unidad_id'] ?? null;

        $exp = $this->expedienteModel->find($expedienteId);
        if (!$exp) {
            $this->redirect('/mi-unidad', ['error' => 'Expediente no encontrado.']);
        }

        if ((int)$exp['unidad_actual_id'] !== (int)$unidadId && !in_array('superadmin', $user['roles'] ?? [], true)) {
            $this->redirect('/mi-unidad', ['error' => 'Este expediente no se encuentra asignado a su unidad.']);
        }

        try {
            Database::transaction(function($pdo) use ($exp, $expedienteId, $user, $unidadId) {
                $estadoModel = new EstadoExpediente();
                $nuevoEstado = $estadoModel->findByCodigo('RECEPCIONADO');
                $nuevoEstadoId = $nuevoEstado ? (int)$nuevoEstado['id'] : 6;

                // 1. Actualizar estado a RECEPCIONADO y usuario responsable
                $this->expedienteModel->update($expedienteId, [
                    'estado_id' => $nuevoEstadoId,
                    'usuario_responsable_id' => (int)$user['id']
                ]);

                // 2. Insertar movimiento formal de recepción con sello de tiempo e IP
                $this->movimientoModel->record(
                    $expedienteId,
                    'RECEPCION',
                    (int)$exp['unidad_actual_id'],
                    (int)$unidadId,
                    (int)$user['id'],
                    (int)$exp['estado_id'],
                    $nuevoEstadoId,
                    "Expediente recepcionado formalmente por {$user['nombre_completo']} ({$user['cargo']}). En proceso de atención técnica.",
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                Auditoria::log((int)$user['id'], 'RECEPCION_EXPEDIENTE', 'unidad', $expedienteId, [
                    'ip' => $this->request->ip()
                ]);
            });

            $this->redirect('/mi-unidad?tab=en_tramite', [
                'success' => "Expediente {$exp['numero_expediente']} recepcionado formalmente con registro de fecha, hora e IP."
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/mi-unidad', ['error' => 'Error al recepcionar: ' . $e->getMessage()]);
        }
    }

    public function showResponder(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $exp = $this->expedienteModel->findWithRelations($expedienteId);

        if (!$exp) {
            $this->redirect('/mi-unidad', ['error' => 'Expediente no encontrado.']);
        }

        $documentos = $this->documentoModel->getByExpediente($expedienteId);
        $movimientos = $this->movimientoModel->getHistory($expedienteId);

        $this->view('unidad.responder', [
            'title' => 'Emitir Respuesta a Expediente ' . $exp['numero_expediente'],
            'expediente' => $exp,
            'documentos' => $documentos,
            'movimientos' => $movimientos
        ], 'app');
    }

    public function storeResponder(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $user = $this->user();
        $unidadId = $user['unidad_id'] ?? 1;

        $descripcion = trim((string)$this->request->post('descripcion', ''));
        $resultado = trim((string)$this->request->post('resultado', 'FAVORABLE'));
        $obsPublica = trim((string)$this->request->post('observacion_publica', ''));
        $obsInterna = trim((string)$this->request->post('observacion_interna', ''));

        $validator = Validator::make([
            'descripcion' => $descripcion,
            'resultado' => $resultado
        ], [
            'descripcion' => 'required',
            'resultado' => 'required'
        ]);

        if ($validator->fails()) {
            $this->redirect("/mi-unidad/{$expedienteId}/responder", [
                'error' => $validator->firstOfAll()
            ]);
        }

        $exp = $this->expedienteModel->find($expedienteId);
        if (!$exp) {
            $this->redirect('/mi-unidad', ['error' => 'Expediente no encontrado.']);
        }

        try {
            Database::transaction(function($pdo) use ($exp, $expedienteId, $user, $unidadId, $descripcion, $resultado, $obsPublica, $obsInterna) {
                // 1. Subir documento de respuesta técnica si fue adjuntado
                if (!empty($_FILES['documento_respuesta']['name'])) {
                    $uploaded = FileUploader::upload($_FILES['documento_respuesta'], 'documents');
                    $this->documentoModel->insert([
                        'expediente_id' => $expedienteId,
                        'usuario_id' => (int)$user['id'],
                        'nombre_original' => $uploaded['original_name'],
                        'nombre_archivo' => $uploaded['file_name'],
                        'ruta_archivo' => $uploaded['relative_path'],
                        'mime_type' => $uploaded['mime_type'],
                        'tamanio_bytes' => $uploaded['size_bytes'],
                        'hash_sha256' => $uploaded['hash_sha256'],
                        'tipo_documento' => 'INFORME_RESPUESTA'
                    ]);
                }

                // 2. Subir anexos adicionales si hubiese
                if (!empty($_FILES['anexos']['name'][0])) {
                    $totalFiles = count($_FILES['anexos']['name']);
                    for ($i = 0; $i < $totalFiles; $i++) {
                        if ($_FILES['anexos']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                            continue;
                        }
                        $single = [
                            'name'     => $_FILES['anexos']['name'][$i],
                            'type'     => $_FILES['anexos']['type'][$i],
                            'tmp_name' => $_FILES['anexos']['tmp_name'][$i],
                            'error'    => $_FILES['anexos']['error'][$i],
                            'size'     => $_FILES['anexos']['size'][$i],
                        ];
                        $up = FileUploader::upload($single, 'documents');
                        $this->documentoModel->insert([
                            'expediente_id' => $expedienteId,
                            'usuario_id' => (int)$user['id'],
                            'nombre_original' => $up['original_name'],
                            'nombre_archivo' => $up['file_name'],
                            'ruta_archivo' => $up['relative_path'],
                            'mime_type' => $up['mime_type'],
                            'tamanio_bytes' => $up['size_bytes'],
                            'hash_sha256' => $up['hash_sha256'],
                            'tipo_documento' => 'ANEXO'
                        ]);
                    }
                }

                // 3. Cambiar estado a RESPONDIDO y derivar de regreso a Dirección General (Unidad 1)
                $estadoModel = new EstadoExpediente();
                $nuevoEstado = $estadoModel->findByCodigo('RESPONDIDO');
                $nuevoEstadoId = $nuevoEstado ? (int)$nuevoEstado['id'] : 11;

                $this->expedienteModel->update($expedienteId, [
                    'estado_id' => $nuevoEstadoId,
                    'unidad_actual_id' => 1, // Regresa a Dirección General
                    'observacion_publica' => !empty($obsPublica) ? $obsPublica : "Atendido con informe técnico de la unidad. En revisión directiva final."
                ]);

                // 4. Movimiento de trazabilidad inmutable
                $observacionCompleta = "Informe de Atención ({$resultado}): {$descripcion}";
                if (!empty($obsInterna)) {
                    $observacionCompleta .= " | Nota Interna: {$obsInterna}";
                }

                $this->movimientoModel->record(
                    $expedienteId,
                    'RESPUESTA',
                    (int)$unidadId,
                    1, // Dirección General
                    (int)$user['id'],
                    (int)$exp['estado_id'],
                    $nuevoEstadoId,
                    $observacionCompleta,
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                // 5. Notificar a Dirección General
                Notificacion::send(
                    null,
                    1,
                    "Respuesta de Unidad Recibida: {$exp['numero_expediente']}",
                    "La unidad ha emitido su informe técnico ({$resultado}). Pendiente de su aprobación final.",
                    "/direccion?tab=respuestas",
                    'success'
                );

                Auditoria::log((int)$user['id'], 'RESPUESTA_DE_UNIDAD', 'unidad', $expedienteId, null, [
                    'resultado' => $resultado,
                    'descripcion' => $descripcion
                ]);
            });

            $this->redirect('/mi-unidad?tab=respondidos', [
                'success' => "Respuesta remitida formalmente a Dirección General para su aprobación final."
            ]);
        } catch (\Throwable $e) {
            $this->redirect("/mi-unidad/{$expedienteId}/responder", [
                'error' => 'Error al emitir respuesta: ' . $e->getMessage()
            ]);
        }
    }
}
