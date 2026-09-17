<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Unidad;
use App\Models\EstadoExpediente;
use App\Models\TipoTramite;
use App\Models\Configuracion;
use App\Models\Auditoria;

class ReporteController extends Controller
{
    private Unidad $unidadModel;
    private EstadoExpediente $estadoModel;
    private TipoTramite $tramiteModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->unidadModel = new Unidad();
        $this->estadoModel = new EstadoExpediente();
        $this->tramiteModel = new TipoTramite();
    }

    public function index(): void
    {
        $filters = $this->getFilters();
        $data = $this->generateReportData($filters);

        $unidades = $this->unidadModel->allActive();
        $estados = $this->estadoModel->all('orden ASC');
        $tramites = $this->tramiteModel->all('nombre ASC');

        Auditoria::log((int)$this->userId(), 'CONSULTAR_REPORTES', 'reportes');

        $this->view('reportes.index', array_merge($data, [
            'title' => 'Reportes Estadísticos y Métricas Institucionales',
            'filters' => $filters,
            'unidades' => $unidades,
            'estados' => $estados,
            'tramites' => $tramites
        ]), 'app');
    }

    public function imprimir(): void
    {
        $filters = $this->getFilters();
        $data = $this->generateReportData($filters);
        $config = Configuracion::getAll();

        Auditoria::log((int)$this->userId(), 'IMPRIMIR_REPORTE', 'reportes');

        $this->view('reportes.imprimir', array_merge($data, [
            'title' => 'Reporte Estadístico Oficial - Mesa de Partes',
            'filters' => $filters,
            'config' => $config
        ]), null); // View without main layout, self-contained printable template
    }

    public function csv(): void
    {
        $filters = $this->getFilters();
        $data = $this->generateReportData($filters);
        $expedientes = $data['expedientes'];

        $filename = 'reporte_expedientes_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'w');

        // BOM UTF-8 para Excel
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Cabeceras de columnas
        fputcsv($output, [
            'N° Expediente',
            'Código Seguimiento',
            'Fecha Registro',
            'DNI',
            'Apellidos y Nombres',
            'Celular',
            'Correo Electrónico',
            'Procedimiento FUT',
            'Unidad Actual',
            'Estado',
            'Prioridad',
            'Asunto / Solicitud'
        ], ';');

        foreach ($expedientes as $exp) {
            $solicitante = trim($exp['apellido_paterno'] . ' ' . $exp['apellido_materno'] . ', ' . $exp['nombres']);

            fputcsv($output, [
                $exp['numero_expediente'],
                $exp['codigo_seguimiento'],
                $exp['created_at'],
                $exp['dni'] ?? '',
                $solicitante,
                $exp['celular'] ?? '',
                $exp['correo'] ?? '',
                $exp['tramite_nombre'] ?? '',
                $exp['unidad_actual_nombre'] ?? 'Mesa de Partes',
                $exp['estado_nombre'] ?? '',
                strtoupper($exp['prioridad_nombre'] ?? 'NORMAL'),
                $exp['solicito'] ?? ''
            ], ';');
        }

        fclose($output);
        exit;
    }

    private function getFilters(): array
    {
        return [
            'desde' => $this->request->get('desde', date('Y-m-01')),
            'hasta' => $this->request->get('hasta', date('Y-m-d')),
            'unidad_id' => $this->request->get('unidad_id', ''),
            'estado_id' => $this->request->get('estado_id', ''),
            'tipo_tramite_id' => $this->request->get('tipo_tramite_id', '')
        ];
    }

    private function generateReportData(array $filters): array
    {
        $db = Database::getConnection();

        $where = ["DATE(e.created_at) >= :desde AND DATE(e.created_at) <= :hasta"];
        $params = [
            'desde' => $filters['desde'],
            'hasta' => $filters['hasta']
        ];

        if (!empty($filters['unidad_id'])) {
            $where[] = "e.unidad_actual_id = :unidad_id";
            $params['unidad_id'] = (int)$filters['unidad_id'];
        }
        if (!empty($filters['estado_id'])) {
            $where[] = "e.estado_id = :estado_id";
            $params['estado_id'] = (int)$filters['estado_id'];
        }
        if (!empty($filters['tipo_tramite_id'])) {
            $where[] = "e.tipo_tramite_id = :tipo_tramite_id";
            $params['tipo_tramite_id'] = (int)$filters['tipo_tramite_id'];
        }

        $whereClause = implode(" AND ", $where);

        // Listado detallado de expedientes
        $sqlList = "SELECT e.*, 
                           t.nombre as tramite_nombre, t.codigo as tramite_codigo, t.plazo_dias,
                           u.nombre as unidad_actual_nombre, 
                           es.nombre as estado_nombre, es.color as estado_color, es.codigo as estado_codigo,
                           p.nombre as prioridad_nombre
                    FROM `expedientes` e 
                    LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id 
                    LEFT JOIN `unidades` u ON e.unidad_actual_id = u.id 
                    LEFT JOIN `estados_expediente` es ON e.estado_id = es.id 
                    LEFT JOIN `prioridades` p ON e.prioridad_id = p.id 
                    WHERE {$whereClause} 
                    ORDER BY e.created_at DESC";

        $stmtList = $db->prepare($sqlList);
        $stmtList->execute($params);
        $expedientes = $stmtList->fetchAll();

        // Métricas Resumen
        $total = count($expedientes);
        $atendidos = 0;
        $enTramite = 0;
        $observados = 0;
        $dentroPlazo = 0;
        $vencidos = 0;

        $porUnidad = [];
        $porTramite = [];

        $now = time();

        foreach ($expedientes as $exp) {
            $estCod = $exp['estado_codigo'] ?? '';
            if (in_array($estCod, ['ATENDIDO', 'FINALIZADO', 'APROBADO', 'RESPONDIDO'], true)) {
                $atendidos++;
            } elseif (in_array($estCod, ['OBSERVADO', 'RECHAZADO'], true)) {
                $observados++;
            } else {
                $enTramite++;
            }

            // Cálculo de plazo
            $plazoDias = (int)($exp['plazo_dias'] ?? 15);
            $fechaIngreso = strtotime($exp['created_at']);
            $diasTranscurridos = floor(($now - $fechaIngreso) / 86400);

            if ($diasTranscurridos > $plazoDias && !in_array($estCod, ['FINALIZADO', 'ATENDIDO'], true)) {
                $vencidos++;
            } else {
                $dentroPlazo++;
            }

            // Agrupación por Unidad
            $uName = $exp['unidad_actual_nombre'] ?? 'Mesa de Partes';
            $porUnidad[$uName] = ($porUnidad[$uName] ?? 0) + 1;

            // Agrupación por Trámite
            $tName = $exp['tramite_nombre'] ?? 'Trámite General';
            $porTramite[$tName] = ($porTramite[$tName] ?? 0) + 1;
        }

        arsort($porUnidad);
        arsort($porTramite);

        return [
            'expedientes' => $expedientes,
            'metrics' => [
                'total' => $total,
                'atendidos' => $atendidos,
                'en_tramite' => $enTramite,
                'observados' => $observados,
                'dentro_plazo' => $dentroPlazo,
                'vencidos' => $vencidos
            ],
            'porUnidad' => $porUnidad,
            'porTramite' => array_slice($porTramite, 0, 10)
        ];
    }
}
