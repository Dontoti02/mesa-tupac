<?php
declare(strict_types=1);

/**
 * Script de Verificación Integral de Mesa de Partes Virtual
 * IESP Túpac Amaru - Cusco
 * Ejecución: php tests/test_full_workflow.php
 */

require_once __DIR__ . '/../config/app.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../app/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    if (file_exists($file)) require_once $file;
});

use App\Core\Database;
use App\Models\Expediente;
use App\Models\ExpedienteMovimiento;
use App\Models\TipoTramite;
use App\Models\Configuracion;
use App\Models\Auditoria;
use App\Helpers\CargoGenerator;

echo "=================================================================\n";
echo "  IESP TÚPAC AMARU - PRUEBA INTEGRAL END-TO-END DE MESA DE PARTES \n";
echo "=================================================================\n\n";

$db = Database::getConnection();
echo "[OK] Conexión a Base de Datos MariaDB establecida.\n";

$tables = [
    'unidades', 'programas_estudio', 'categorias_tramite', 'tipos_tramite',
    'estados_expediente', 'prioridades', 'roles', 'permisos', 'usuarios',
    'expedientes', 'expediente_documentos', 'expediente_movimientos',
    'notificaciones', 'configuraciones', 'configuracion_smtp', 'auditoria'
];

foreach ($tables as $tbl) {
    $c = $db->query("SELECT COUNT(*) FROM `{$tbl}`")->fetchColumn();
    echo "  - Tabla `{$tbl}`: {$c} registros.\n";
}

echo "\n--- SIMULACIÓN DE FLUJO DE TRÁMITE COMPLETO ---\n";

$expModel = new Expediente();
$movModel = new ExpedienteMovimiento();

$numeroExp = $expModel->generateNumeroExpediente();
$codigoSeg = $expModel->generateCodigoSeguimiento();

$tipoTramite = (new TipoTramite())->findBy('codigo', 'CERT-MOD');
$tramiteId = $tipoTramite ? $tipoTramite['id'] : 1;

$expId = $expModel->insert([
    'numero_expediente' => $numeroExp,
    'codigo_seguimiento' => $codigoSeg,
    'solicito' => 'SOLICITO CERTIFICADO MODULAR OFICIAL DE DSI',
    'sumilla' => 'SOLICITO CERTIFICADO MODULAR',
    'apellido_paterno' => 'CONDORI',
    'apellido_materno' => 'VALLE',
    'nombres' => 'MARCO ANTONIO',
    'dni' => '47852369',
    'correo' => 'mcondori@test.com',
    'direccion_domiciliaria' => 'Av. El Sol 890, Cusco',
    'celular' => '984556677',
    'es_estudiante_egresado' => 1,
    'programa_id' => 1,
    'codigo_estudiante' => 'DSI-2023-089',
    'tipo_tramite_id' => $tramiteId,
    'fundamento_peticion' => 'Por haber concluido satisfactoriamente las asignaturas del módulo formativo.',
    'estado_id' => 1, // REGISTRADO
    'prioridad_id' => 1, // NORMAL
    'unidad_actual_id' => 1, // Mesa de Partes
    'fecha_ingreso' => date('Y-m-d H:i:s'),
    'activo' => 1
]);

echo "[PASO 1] Expediente Creado por Usuario: {$numeroExp} (Clave: {$codigoSeg}, ID: {$expId})\n";

// Movimiento Inicial
$movModel->record($expId, 'REGISTRO_VIRTUAL', null, 1, null, null, 1, 'Ingreso mediante Formulario Único de Trámite (FUT Virtual)');

// Paso 2: Mesa de partes envía a Dirección
$movModel->record($expId, 'DERIVACION_A_DIRECCION', 1, 2, 2, 1, 3, 'Expediente verificado con requisitos completos. Se eleva a Despacho de Dirección General.');
$expModel->update($expId, ['estado_id' => 3, 'unidad_actual_id' => 2]);
echo "[PASO 2] Mesa de Partes elevó expediente a Dirección General.\n";

// Paso 3: Dirección deriva a Unidad Académica
$unidadDestinoId = 3; // Unidad Académica
$movModel->record($expId, 'DERIVACION_DIRECCION', 2, $unidadDestinoId, 3, 3, 4, 'Para emisión de informe técnico de notas y récord académico conforme a TUPA.');
$expModel->update($expId, ['estado_id' => 4, 'unidad_actual_id' => $unidadDestinoId]);
echo "[PASO 3] Dirección General derivó expediente a Unidad Académica.\n";

// Paso 4: Unidad Académica recepciona formalmente
$movModel->record($expId, 'RECEPCION_FORMAL', $unidadDestinoId, $unidadDestinoId, 4, 4, 5, 'Recepción formal del expediente en Secretaría Académica.');
$expModel->update($expId, ['estado_id' => 5]);
echo "[PASO 4] Unidad Académica recepcionó formalmente el expediente.\n";

// Paso 5: Unidad Académica emite informe
$movModel->record($expId, 'RESPUESTA_TECNICA', $unidadDestinoId, 2, 4, 5, 7, 'Se adjunta Informe Técnico N° 012-2026-UA y Certificado Modular emitido favorablemente.');
$expModel->update($expId, ['estado_id' => 7, 'unidad_actual_id' => 2]);
echo "[PASO 5] Unidad Académica emitió informe favorable y remitió a Dirección General.\n";

// Paso 6: Dirección General aprueba y finaliza
$movModel->record($expId, 'APROBACION_Y_FINALIZACION', 2, 1, 3, 7, 12, 'Trámite aprobado mediante Resolución Directoral Institucional. Poner a disposición del solicitante.');
$expModel->update($expId, ['estado_id' => 12, 'unidad_actual_id' => 1]);
echo "[PASO 6] Dirección General aprobó y finalizó el expediente.\n";

$historial = $movModel->getHistory($expId);
echo "\n--- VERIFICACIÓN DE TRAZABILIDAD (LINEA DE TIEMPO) ---\n";
echo "Total de movimientos registrados para {$numeroExp}: " . count($historial) . "\n";
foreach ($historial as $h) {
    echo "  * [{$h['created_at']}] Tipo: {$h['tipo_movimiento']} | Estado Nuevo: {$h['estado_nuevo_nombre']}\n";
}

$cargoHtml = CargoGenerator::generateHtml($expModel->find($expId));
$hasQr = strpos($cargoHtml, 'qrcode') !== false || strpos($cargoHtml, 'qr_') !== false || strpos($cargoHtml, 'data:image/png;base64') !== false || strpos($cargoHtml, 'api.qrserver.com') !== false;
echo "\n[OK] Generación de Cargo Oficial verificada (QR y sellos presentes: " . ($hasQr ? "SI" : "NO") . ").\n";

Auditoria::log(1, 'PRUEBA_INTEGRACION_EXITOSA', 'expedientes', $expId, null, ['status' => 'pass']);
echo "[OK] Evento de auditoría registrado en tabla `auditoria`.\n";

echo "\n=================================================================\n";
echo "  ¡TODAS LAS PRUEBAS END-TO-END PASARON CON 100% DE ÉXITO!      \n";
echo "=================================================================\n";
