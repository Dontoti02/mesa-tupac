<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Expediente;
use App\Models\ExpedienteDocumento;
use App\Models\ExpedienteMovimiento;
use App\Models\Unidad;
use App\Models\ProgramaEstudio;
use App\Models\TipoTramite;
use App\Models\EstadoExpediente;
use App\Models\Prioridad;
use App\Models\Auditoria;

class ExpedienteController extends Controller
{
    private Expediente $expedienteModel;
    private ExpedienteDocumento $documentoModel;
    private ExpedienteMovimiento $movimientoModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->expedienteModel = new Expediente();
        $this->documentoModel = new ExpedienteDocumento();
        $this->movimientoModel = new ExpedienteMovimiento();
    }

    public function index(): void
    {
        // Parámetros de filtros avanzados (Sección 17)
        $q = trim((string)$this->request->get('q', ''));
        $estadoId = $this->request->get('estado_id');
        $unidadId = $this->request->get('unidad_id');
        $prioridadId = $this->request->get('prioridad_id');
        $programaId = $this->request->get('programa_id');
        $tipoTramiteId = $this->request->get('tipo_tramite_id');
        $fechaDesde = $this->request->get('fecha_desde');
        $fechaHasta = $this->request->get('fecha_hasta');

        // Paginación backend (Sección 41)
        $perPage = (int)$this->request->get('per_page', 20);
        if (!in_array($perPage, [20, 50, 100], true)) {
            $perPage = 20;
        }
        $page = max(1, (int)$this->request->get('page', 1));
        $offset = ($page - 1) * $perPage;

        // Construir consulta SQL dinámica segura con parámetros enlazados
        $where = ["e.activo = 1"];
        $params = [];

        if (!empty($q)) {
            $where[] = "(e.numero_expediente LIKE :q OR e.codigo_seguimiento LIKE :q OR e.dni LIKE :q OR e.nombres LIKE :q OR e.apellido_paterno LIKE :q OR e.apellido_materno LIKE :q OR e.sumilla LIKE :q)";
            $params['q'] = "%{$q}%";
        }
        if (!empty($estadoId)) {
            $where[] = "e.estado_id = :estado_id";
            $params['estado_id'] = (int)$estadoId;
        }
        if (!empty($unidadId)) {
            $where[] = "e.unidad_actual_id = :unidad_id";
            $params['unidad_id'] = (int)$unidadId;
        }
        if (!empty($prioridadId)) {
            $where[] = "e.prioridad_id = :prioridad_id";
            $params['prioridad_id'] = (int)$prioridadId;
        }
        if (!empty($programaId)) {
            $where[] = "e.programa_id = :programa_id";
            $params['programa_id'] = (int)$programaId;
        }
        if (!empty($tipoTramiteId)) {
            $where[] = "e.tipo_tramite_id = :tipo_tramite_id";
            $params['tipo_tramite_id'] = (int)$tipoTramiteId;
        }
        if (!empty($fechaDesde)) {
            $where[] = "DATE(e.fecha_ingreso) >= :fecha_desde";
            $params['fecha_desde'] = $fechaDesde;
        }
        if (!empty($fechaHasta)) {
            $where[] = "DATE(e.fecha_ingreso) <= :fecha_hasta";
            $params['fecha_hasta'] = $fechaHasta;
        }

        $whereSql = implode(" AND ", $where);

        // Conteo total de registros para paginación
        $stmtCount = $this->expedienteModel->db()->prepare("SELECT COUNT(*) as total FROM `expedientes` e WHERE {$whereSql}");
        $stmtCount->execute($params);
        $totalRecords = (int)$stmtCount->fetch()['total'];
        $totalPages = (int)ceil($totalRecords / $perPage);

        // Obtener registros de la página
        $sql = "SELECT e.*, 
                       es.nombre as estado_nombre, es.color as estado_color, es.icono as estado_icono,
                       p.nombre as prioridad_nombre, p.color as prioridad_color,
                       t.nombre as tramite_nombre,
                       u.nombre as unidad_nombre,
                       prog.nombre as programa_nombre
                FROM `expedientes` e 
                LEFT JOIN `estados_expediente` es ON e.estado_id = es.id 
                LEFT JOIN `prioridades` p ON e.prioridad_id = p.id 
                LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id 
                LEFT JOIN `unidades` u ON e.unidad_actual_id = u.id 
                LEFT JOIN `programas_estudio` prog ON e.programa_id = prog.id 
                WHERE {$whereSql} 
                ORDER BY e.fecha_ingreso DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $this->expedienteModel->db()->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();
        $expedientes = $stmt->fetchAll();

        // Catálogos para los selectores de filtros
        $estados = (new EstadoExpediente())->all('orden ASC');
        $unidades = (new Unidad())->allActive();
        $prioridades = (new Prioridad())->all('orden ASC');
        $programas = (new ProgramaEstudio())->allActive();
        $tramites = (new TipoTramite())->all('orden ASC');

        $this->view('expedientes.index', [
            'title' => 'Gestión General de Expedientes',
            'expedientes' => $expedientes,
            'totalRecords' => $totalRecords,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'perPage' => $perPage,
            'estados' => $estados,
            'unidades' => $unidades,
            'prioridades' => $prioridades,
            'programas' => $programas,
            'tramites' => $tramites,
            'filters' => [
                'q' => $q,
                'estado_id' => $estadoId,
                'unidad_id' => $unidadId,
                'prioridad_id' => $prioridadId,
                'programa_id' => $programaId,
                'tipo_tramite_id' => $tipoTramiteId,
                'fecha_desde' => $fechaDesde,
                'fecha_hasta' => $fechaHasta
            ]
        ], 'app');
    }

    public function show(Request $request, Response $response, string $id): void
    {
        $expedienteId = (int)$id;
        $expediente = $this->expedienteModel->findWithRelations($expedienteId);

        if (!$expediente) {
            $this->redirect('/expedientes', [
                'error' => 'El expediente solicitado no existe o ha sido deshabilitado.'
            ]);
        }

        $documentos = $this->documentoModel->getByExpediente($expedienteId);
        $movimientos = $this->movimientoModel->getHistory($expedienteId, false); // Trazabilidad completa con notas internas

        $this->view('expedientes.show', [
            'title' => 'Expediente ' . $expediente['numero_expediente'],
            'expediente' => $expediente,
            'documentos' => $documentos,
            'movimientos' => $movimientos
        ], 'app');
    }

    public function descargarDocumento(Request $request, Response $response, string $id, string $docId): void
    {
        $expedienteId = (int)$id;
        $documentoId = (int)$docId;

        $doc = $this->documentoModel->find($documentoId);

        if (!$doc || (int)$doc['expediente_id'] !== $expedienteId) {
            $this->response->setStatusCode(404);
            echo "Documento no encontrado o no pertenece a este expediente.";
            exit;
        }

        $baseDir = dirname(__DIR__, 2);
        $fullPath = $baseDir . '/' . ltrim($doc['ruta_archivo'], '/');

        if (!file_exists($fullPath)) {
            $this->response->setStatusCode(404);
            echo "El archivo físico no se encuentra en el repositorio seguro del servidor.";
            exit;
        }

        // Auditoría de descarga de archivo
        $user = $this->user();
        if ($user) {
            Auditoria::log((int)$user['id'], 'DESCARGA_DOCUMENTO', 'expedientes', $expedienteId, null, [
                'documento_id' => $documentoId,
                'archivo' => $doc['nombre_original']
            ]);
        }

        $this->response->download($fullPath, $doc['nombre_original'], $doc['mime_type']);
    }
}
