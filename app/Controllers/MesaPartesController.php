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
use App\Models\TipoTramite;
use App\Models\ProgramaEstudio;
use App\Models\EstadoExpediente;
use App\Models\Prioridad;
use App\Models\Notificacion;
use App\Models\Auditoria;
use App\Helpers\Validator;
use App\Helpers\FileUploader;

class MesaPartesController extends Controller
{
    private Expediente $expedienteModel;
    private ExpedienteDocumento $documentoModel;
    private ExpedienteMovimiento $movimientoModel;
    private TipoTramite $tipoTramiteModel;
    private ProgramaEstudio $programaModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->expedienteModel = new Expediente();
        $this->documentoModel = new ExpedienteDocumento();
        $this->movimientoModel = new ExpedienteMovimiento();
        $this->tipoTramiteModel = new TipoTramite();
        $this->programaModel = new ProgramaEstudio();
    }

    public function index(): void
    {
        $tab = $this->request->get('tab', 'pendientes');
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

        // Filtro por pestañas de Mesa de Partes
        switch ($tab) {
            case 'pendientes':
                $sql .= " AND es.codigo IN ('RECIBIDO', 'REGISTRADO')";
                break;
            case 'enviados':
                $sql .= " AND es.codigo = 'ENVIADO_A_DIRECCION'";
                break;
            case 'observados':
                $sql .= " AND es.codigo = 'OBSERVADO'";
                break;
            case 'finalizados':
                $sql .= " AND es.codigo IN ('FINALIZADO', 'APROBADO')";
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

        // Conteos para los badges de las pestañas
        $db = Database::getConnection();
        $cRecibidos = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('RECIBIDO', 'REGISTRADO') AND e.activo = 1")->fetch()['t'];
        $cEnviados = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo = 'ENVIADO_A_DIRECCION' AND e.activo = 1")->fetch()['t'];
        $cObservados = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo = 'OBSERVADO' AND e.activo = 1")->fetch()['t'];
        $cFinalizados = (int)$db->query("SELECT COUNT(*) as t FROM expedientes e JOIN estados_expediente es ON e.estado_id = es.id WHERE es.codigo IN ('FINALIZADO', 'APROBADO') AND e.activo = 1")->fetch()['t'];

        $this->view('mesa_partes.index', [
            'title' => 'Bandeja de Mesa de Partes',
            'expedientes' => $expedientes,
            'currentTab' => $tab,
            'search' => $search,
            'counts' => [
                'pendientes' => $cRecibidos,
                'enviados' => $cEnviados,
                'observados' => $cObservados,
                'finalizados' => $cFinalizados
            ]
        ], 'app');
    }

    public function showRegistrar(): void
    {
        $programas = $this->programaModel->allActive();
        $tramitesGrouped = $this->tipoTramiteModel->allActiveGroupedByCategory();

        $this->view('mesa_partes.registrar', [
            'title' => 'Registro Presencial de Trámite',
            'programas' => $programas,
            'tramitesGrouped' => $tramitesGrouped
        ], 'app');
    }

    public function storeRegistrar(): void
    {
        $data = $this->request->all();
        $user = $this->user();

        $rules = [
            'solicito' => 'required|max:255',
            'sumilla' => 'required',
            'dni' => 'required|dni',
            'apellido_paterno' => 'required|max:100',
            'apellido_materno' => 'required|max:100',
            'nombres' => 'required|max:100',
            'correo' => 'required|email|max:100',
            'celular' => 'required|max:30',
            'direccion_domiciliaria' => 'required|max:255',
            'tipo_tramite_id' => 'required|numeric'
        ];

        $isEstudiante = !empty($data['es_estudiante_egresado']) && (int)$data['es_estudiante_egresado'] === 1;
        if ($isEstudiante) {
            $rules['programa_id'] = 'required|numeric';
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            $this->redirect('/mesa-partes/registrar', [
                'error' => $validator->firstOfAll()
            ]);
        }

        try {
            $expedienteId = Database::transaction(function($pdo) use ($data, $isEstudiante, $user) {
                $estadoModel = new EstadoExpediente();
                $estado = $estadoModel->findByCodigo('REGISTRADO');
                $estadoId = $estado ? (int)$estado['id'] : 2;

                $prioridadModel = new Prioridad();
                $prioridad = $prioridadModel->findByCodigo($data['prioridad'] ?? 'NORMAL') ?? $prioridadModel->findByCodigo('NORMAL');
                $prioridadId = $prioridad ? (int)$prioridad['id'] : 1;

                $numExp = $this->expedienteModel->generateNumeroExpediente();
                $code = $this->expedienteModel->generateCodigoSeguimiento();

                $id = $this->expedienteModel->insert([
                    'numero_expediente' => $numExp,
                    'codigo_seguimiento' => $code,
                    'solicito' => trim((string)$data['solicito']),
                    'sumilla' => trim((string)$data['sumilla']),
                    'apellido_paterno' => mb_strtoupper(trim((string)$data['apellido_paterno']), 'UTF-8'),
                    'apellido_materno' => mb_strtoupper(trim((string)$data['apellido_materno']), 'UTF-8'),
                    'nombres' => mb_strtoupper(trim((string)$data['nombres']), 'UTF-8'),
                    'dni' => trim((string)$data['dni']),
                    'correo' => strtolower(trim((string)$data['correo'])),
                    'direccion_domiciliaria' => trim((string)$data['direccion_domiciliaria']),
                    'celular' => trim((string)$data['celular']),
                    'es_estudiante_egresado' => $isEstudiante ? 1 : 0,
                    'programa_id' => $isEstudiante ? (int)$data['programa_id'] : null,
                    'codigo_estudiante' => $isEstudiante ? trim((string)($data['codigo_estudiante'] ?? '')) : null,
                    'anio_ingreso' => $isEstudiante ? trim((string)($data['anio_ingreso'] ?? '')) : null,
                    'anio_egreso' => $isEstudiante ? trim((string)($data['anio_egreso'] ?? '')) : null,
                    'tipo_tramite_id' => (int)$data['tipo_tramite_id'],
                    'fundamento_peticion' => trim((string)($data['fundamento_peticion'] ?? 'Trámite ingresado presencialmente en ventanilla.')),
                    'estado_id' => $estadoId,
                    'prioridad_id' => $prioridadId,
                    'unidad_actual_id' => 1, // Mesa de Partes
                    'fecha_ingreso' => date('Y-m-d H:i:s'),
                    'activo' => 1
                ]);

                // Subida de adjuntos presenciales si hubiese
                if (!empty($_FILES['adjuntos']['name'][0])) {
                    $totalFiles = count($_FILES['adjuntos']['name']);
                    for ($i = 0; $i < $totalFiles; $i++) {
                        if ($_FILES['adjuntos']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                            continue;
                        }
                        $single = [
                            'name'     => $_FILES['adjuntos']['name'][$i],
                            'type'     => $_FILES['adjuntos']['type'][$i],
                            'tmp_name' => $_FILES['adjuntos']['tmp_name'][$i],
                            'error'    => $_FILES['adjuntos']['error'][$i],
                            'size'     => $_FILES['adjuntos']['size'][$i],
                        ];
                        $up = FileUploader::upload($single, 'documents');
                        $this->documentoModel->insert([
                            'expediente_id' => $id,
                            'usuario_id' => (int)$user['id'],
                            'nombre_original' => $up['original_name'],
                            'nombre_archivo' => $up['file_name'],
                            'ruta_archivo' => $up['relative_path'],
                            'mime_type' => $up['mime_type'],
                            'tamanio_bytes' => $up['size_bytes'],
                            'hash_sha256' => $up['hash_sha256'],
                            'tipo_documento' => 'ADJUNTO_INICIAL'
                        ]);
                    }
                }

                // Trazabilidad inicial
                $this->movimientoModel->record(
                    $id,
                    'REGISTRO_PRESENCIAL',
                    1,
                    1,
                    (int)$user['id'],
                    null,
                    $estadoId,
                    'Ingreso de trámite presencial en ventanilla de Mesa de Partes.',
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                Auditoria::log((int)$user['id'], 'REGISTRO_PRESENCIAL', 'mesa_partes', $id, null, [
                    'numero_expediente' => $numExp
                ]);

                return $id;
            });

            $this->redirect("/mesa-partes", [
                'success' => "Trámite registrado correctamente con N° de Expediente."
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/mesa-partes/registrar', [
                'error' => 'Error al registrar trámite presencial: ' . $e->getMessage()
            ]);
        }
    }

    public function enviarDireccion(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $user = $this->user();
        $observacion = trim((string)$this->request->post('observacion', 'Remitido formalmente a Dirección General para su evaluación y derivación ordinaria.'));

        $exp = $this->expedienteModel->find($expedienteId);
        if (!$exp) {
            $this->redirect('/mesa-partes', ['error' => 'Expediente no encontrado.']);
        }

        try {
            Database::transaction(function($pdo) use ($exp, $expedienteId, $user, $observacion) {
                $estadoModel = new EstadoExpediente();
                $nuevoEstado = $estadoModel->findByCodigo('ENVIADO_A_DIRECCION');
                $nuevoEstadoId = $nuevoEstado ? (int)$nuevoEstado['id'] : 3;

                // 1. Actualizar estado y ubicación del expediente
                $this->expedienteModel->update($expedienteId, [
                    'estado_id' => $nuevoEstadoId,
                    'unidad_actual_id' => 1 // Dirección General
                ]);

                // 2. Insertar movimiento de trazabilidad inmutable
                $this->movimientoModel->record(
                    $expedienteId,
                    'ENVIO_DIRECCION',
                    $exp['unidad_actual_id'],
                    1, // Dirección
                    (int)$user['id'],
                    (int)$exp['estado_id'],
                    $nuevoEstadoId,
                    $observacion,
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                // 3. Notificación a Dirección General (Unidad 1)
                Notificacion::send(
                    null,
                    1,
                    "Expediente Remitido a Dirección: {$exp['numero_expediente']}",
                    "Mesa de Partes ha remitido el expediente para su revisión y derivación.",
                    "/direccion",
                    'warning'
                );

                // 4. Auditoría
                Auditoria::log(
                    (int)$user['id'],
                    'ENVIO_A_DIRECCION',
                    'mesa_partes',
                    $expedienteId,
                    ['estado_id' => $exp['estado_id']],
                    ['estado_id' => $nuevoEstadoId, 'observacion' => $observacion]
                );
            });

            $this->redirect('/mesa-partes', [
                'success' => "El expediente {$exp['numero_expediente']} fue remitido exitosamente a Dirección General."
            ]);
        } catch (\Throwable $e) {
            $this->redirect('/mesa-partes', [
                'error' => 'Error al remitir expediente a Dirección: ' . $e->getMessage()
            ]);
        }
    }
}
