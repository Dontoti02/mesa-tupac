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
use App\Helpers\CargoGenerator;

class TramiteController extends Controller
{
    private Expediente $expedienteModel;
    private TipoTramite $tipoTramiteModel;
    private ProgramaEstudio $programaModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->expedienteModel = new Expediente();
        $this->tipoTramiteModel = new TipoTramite();
        $this->programaModel = new ProgramaEstudio();
    }

    public function inicio(): void
    {
        $this->view('public.inicio', [
            'title' => 'Inicio - Mesa de Partes Virtual'
        ], 'public');
    }

    public function catalogo(): void
    {
        $tramites = $this->tipoTramiteModel->allWithDetails();
        $categorias = (new \App\Models\CategoriaTramite())->all('orden ASC');

        $this->view('public.catalogo', [
            'title' => 'Catálogo de Trámites y Requisitos - FUT Digital',
            'tramites' => $tramites,
            'categorias' => $categorias
        ], 'public');
    }

    public function showFut(): void
    {
        $programas = $this->programaModel->allActive();
        $tramitesGrouped = $this->tipoTramiteModel->allActiveGroupedByCategory();

        $this->view('public.fut', [
            'title' => 'Formulario Único de Trámite (FUT)',
            'programas' => $programas,
            'tramitesGrouped' => $tramitesGrouped
        ], 'public');
    }

    public function processFut(): void
    {
        $data = $this->request->all();

        // Reglas de validación
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
            'tipo_tramite_id' => 'required|numeric',
            'fundamento_peticion' => 'required',
            'declaracion_jurada' => 'required'
        ];

        $isEstudiante = !empty($data['es_estudiante_egresado']) && (int)$data['es_estudiante_egresado'] === 1;
        if ($isEstudiante) {
            $rules['programa_id'] = 'required|numeric';
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $this->redirect('/tramite', [
                'error' => $validator->firstOfAll()
            ]);
        }

        try {
            // Ejecutar registro completo dentro de una transacción atómica PDO
            $nuevoId = Database::transaction(function($pdo) use ($data, $isEstudiante) {
                // 1. Obtener estado inicial (REGISTRADO) y prioridad NORMAL
                $estadoModel = new EstadoExpediente();
                $estado = $estadoModel->findByCodigo('REGISTRADO') ?? $estadoModel->findByCodigo('RECIBIDO');
                $estadoId = $estado ? (int)$estado['id'] : 2;

                $prioridadModel = new Prioridad();
                $prioridad = $prioridadModel->findByCodigo('NORMAL');
                $prioridadId = $prioridad ? (int)$prioridad['id'] : 1;

                // Ubicación inicial: Unidad 1 (Mesa de Partes / Dirección)
                $unidadActualId = 1;

                // 2. Generar correlativos oficiales
                $numeroExpediente = $this->expedienteModel->generateNumeroExpediente();
                $codigoSeguimiento = $this->expedienteModel->generateCodigoSeguimiento();

                // 3. Crear Expediente
                $expedienteId = $this->expedienteModel->insert([
                    'numero_expediente' => $numeroExpediente,
                    'codigo_seguimiento' => $codigoSeguimiento,
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
                    'fundamento_peticion' => trim((string)$data['fundamento_peticion']),
                    'estado_id' => $estadoId,
                    'prioridad_id' => $prioridadId,
                    'unidad_actual_id' => $unidadActualId,
                    'fecha_ingreso' => date('Y-m-d H:i:s'),
                    'activo' => 1
                ]);

                // 4. Subir y guardar documentos adjuntos
                $docModel = new ExpedienteDocumento();
                if (!empty($_FILES['adjuntos']['name'][0])) {
                    $totalFiles = count($_FILES['adjuntos']['name']);
                    for ($i = 0; $i < $totalFiles; $i++) {
                        if ($_FILES['adjuntos']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                            continue;
                        }

                        $singleFile = [
                            'name'     => $_FILES['adjuntos']['name'][$i],
                            'type'     => $_FILES['adjuntos']['type'][$i],
                            'tmp_name' => $_FILES['adjuntos']['tmp_name'][$i],
                            'error'    => $_FILES['adjuntos']['error'][$i],
                            'size'     => $_FILES['adjuntos']['size'][$i],
                        ];

                        $uploaded = FileUploader::upload($singleFile, 'documents');

                        $docModel->insert([
                            'expediente_id' => $expedienteId,
                            'usuario_id' => null, // Solicitante externo
                            'nombre_original' => $uploaded['original_name'],
                            'nombre_archivo' => $uploaded['file_name'],
                            'ruta_archivo' => $uploaded['relative_path'],
                            'mime_type' => $uploaded['mime_type'],
                            'tamanio_bytes' => $uploaded['size_bytes'],
                            'hash_sha256' => $uploaded['hash_sha256'],
                            'tipo_documento' => 'ADJUNTO_INICIAL'
                        ]);
                    }
                }

                // 5. Registrar Movimiento Inicial de Trazabilidad
                $movModel = new ExpedienteMovimiento();
                $movModel->record(
                    $expedienteId,
                    'REGISTRO',
                    null,
                    $unidadActualId,
                    null,
                    null,
                    $estadoId,
                    'Trámite registrado exitosamente vía Formulario Único de Trámite (FUT Virtual).',
                    true,
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                // 6. Notificar a Mesa de Partes
                Notificacion::send(
                    null,
                    1, // Mesa de Partes / General
                    "Nuevo Trámite Virtual: {$numeroExpediente}",
                    "El ciudadano {$data['nombres']} {$data['apellido_paterno']} ha registrado una solicitud.",
                    "/expedientes/{$expedienteId}",
                    'info'
                );

                // 7. Auditoría
                Auditoria::log(
                    null,
                    'REGISTRO_FUT_VIRTUAL',
                    'expedientes',
                    $expedienteId,
                    null,
                    [
                        'numero_expediente' => $numeroExpediente,
                        'codigo_seguimiento' => $codigoSeguimiento,
                        'dni' => $data['dni']
                    ],
                    $this->request->ip(),
                    $this->request->userAgent()
                );

                return $expedienteId;
            });

            $this->redirect("/tramite/confirmacion/{$nuevoId}", [
                'success' => 'Su trámite ha sido registrado con éxito. Descargue o imprima su Cargo Oficial.'
            ]);

        } catch (\Throwable $e) {
            error_log('Error en registro FUT: ' . $e->getMessage());
            $this->redirect('/tramite', [
                'error' => 'Ocurrió un error al procesar su solicitud: ' . $e->getMessage()
            ]);
        }
    }

    public function showConfirmacion(Request $request, Response $response, string $id): void
    {
        $expediente = $this->expedienteModel->findWithRelations((int)$id);

        if (!$expediente) {
            $this->redirect('/tramite', [
                'error' => 'El expediente solicitado no fue encontrado.'
            ]);
        }

        $this->view('public.confirmacion', [
            'title' => 'Confirmación de Registro - Expediente ' . $expediente['numero_expediente'],
            'expediente' => $expediente
        ], 'public');
    }

    public function showCargo(Request $request, Response $response, string $id): void
    {
        $expediente = $this->expedienteModel->findWithRelations((int)$id);

        if (!$expediente) {
            $response->setStatusCode(404);
            echo "Expediente no encontrado.";
            exit;
        }

        $docModel = new ExpedienteDocumento();
        $documentos = $docModel->getByExpediente((int)$id);

        $html = CargoGenerator::generateHtml($expediente, $documentos);
        echo $html;
        exit;
    }
}
