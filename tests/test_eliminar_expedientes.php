<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

use App\Core\Database;
use App\Models\Expediente;

/**
 * Renderiza directamente la vista del Registro Maestro para poder capturar el
 * HTML sin que Response::view() finalice el proceso con exit().
 */
function renderRegistroMaestro(array $currentUser): string
{
    $expedientes = [[
        'id' => 1,
        'numero_expediente' => 'EXP-2026-000001',
        'codigo_seguimiento' => 'TA-TEST01',
        'fecha_ingreso' => '2026-10-02 10:00:00',
        'nombres' => 'Juan',
        'apellido_paterno' => 'Perez',
        'dni' => '12345678',
        'programa_nombre' => 'Desarrollo de Sistemas de Información',
        'tramite_nombre' => 'Certificado de Estudios',
        'solicito' => 'Certificado de Estudios',
        'sumilla' => 'Solicitud de prueba',
        'unidad_nombre' => 'Dirección General',
        'estado_nombre' => 'Recibido',
        'estado_color' => '#3B82F6',
        'estado_icono' => 'bi-clock',
        'prioridad_nombre' => 'Normal',
        'prioridad_color' => '#6B7280',
    ]];

    $data = [
        'expedientes' => $expedientes,
        'totalRecords' => count($expedientes),
        'totalPages' => 1,
        'currentPage' => 1,
        'perPage' => 20,
        'estados' => [],
        'unidades' => [],
        'prioridades' => [],
        'programas' => [],
        'tramites' => [],
        'filters' => [
            'q' => '', 'estado_id' => '', 'unidad_id' => '', 'prioridad_id' => '',
            'programa_id' => '', 'tipo_tramite_id' => '', 'fecha_desde' => '', 'fecha_hasta' => '',
        ],
        'currentUser' => $currentUser,
    ];

    extract($data);
    ob_start();
    require dirname(__DIR__) . '/app/Views/expedientes/index.php';
    return (string)ob_get_clean();
}

$superadmin = ['id' => 1, 'roles' => ['superadmin']];
$auditor = ['id' => 2, 'roles' => ['auditor']];

// 1. El superadmin debe ver los controles de eliminación masiva
$htmlSuper = renderRegistroMaestro($superadmin);
$requeridos = ['formExpedientes', '/expedientes/eliminar', 'chk-expediente', 'btnEliminarSeleccionados', 'modalEliminarExpedientes'];
$faltantes = array_filter($requeridos, fn(string $needle): bool => !str_contains($htmlSuper, $needle));
if (!empty($faltantes)) {
    die('ERROR: El listado de superadmin no contiene: ' . implode(', ', $faltantes) . "\n");
}
echo "CONTROLES DE ELIMINACIÓN VISIBLES PARA SUPERADMIN: OK\n";

// 2. Un rol distinto no debe ver ni poder enviar el formulario de eliminación
$htmlAuditor = renderRegistroMaestro($auditor);
if (str_contains($htmlAuditor, 'btnEliminarSeleccionados') || str_contains($htmlAuditor, 'name="expediente_ids[]"')) {
    die("ERROR: El listado de un rol no autorizado expone controles de eliminación.\n");
}
echo "CONTROLES DE ELIMINACIÓN OCULTOS PARA ROLES DISTINTOS: OK\n";

// 3. La ruta de eliminación debe estar restringida a superadmin y protegida por CSRF
$router = new \App\Core\Router(new \App\Core\Request(), new \App\Core\Response());
require dirname(__DIR__) . '/config/routes.php';

$routesProp = (new \ReflectionClass($router))->getProperty('routes');
$routesProp->setAccessible(true);

$rutaEliminar = null;
foreach ($routesProp->getValue($router) as $ruta) {
    if ($ruta['method'] === 'POST' && $ruta['path'] === '/expedientes/eliminar') {
        $rutaEliminar = $ruta;
        break;
    }
}

if ($rutaEliminar === null) {
    die("ERROR: la ruta POST /expedientes/eliminar no está registrada.\n");
}
if (!in_array('RoleMiddleware:superadmin', $rutaEliminar['middlewares'], true)
    || !in_array('CsrfMiddleware', $rutaEliminar['middlewares'], true)) {
    die("ERROR: la ruta de eliminación no está restringida a superadmin y/o protegida por CSRF.\n");
}
echo "RUTA RESTRINGIDA A SUPERADMIN Y PROTEGIDA POR CSRF: OK\n";

// 4. Eliminación lógica múltiple del modelo (dentro de una transacción revertida)
$pdo = Database::getConnection();
$expedienteModel = new Expediente();
$codigo = 'TA-T' . strtoupper(bin2hex(random_bytes(3)));

$pdo->beginTransaction();
$stmt = $pdo->prepare(
    "INSERT INTO `expedientes`
        (`numero_expediente`, `codigo_seguimiento`, `solicito`, `sumilla`,
         `apellido_paterno`, `apellido_materno`, `nombres`, `dni`, `correo`,
         `direccion_domiciliaria`, `celular`, `tipo_tramite_id`, `fundamento_peticion`,
         `estado_id`, `prioridad_id`, `unidad_actual_id`, `fecha_ingreso`)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, 1, 1, 1, NOW())"
);
$stmt->execute([
    'EXP-TEST-DEL-' . $codigo,
    $codigo,
    'Solicitud de prueba',
    'Prueba de eliminación',
    'Prueba',
    'Modelo',
    'Usuario',
    '00000099',
    'prueba@tupacamaru.edu.pe',
    'Dirección de prueba',
    '999999999',
    'Fundamento de prueba',
]);
$nuevoId = (int)$pdo->lastInsertId();

if (count($expedienteModel->resumenPorIds([$nuevoId])) !== 1) {
    $pdo->rollBack();
    die("ERROR: resumenPorIds no devolvió el expediente insertado.\n");
}

$total = $expedienteModel->desactivarMultiples([$nuevoId]);
if ($total !== 1) {
    $pdo->rollBack();
    die("ERROR: desactivarMultiples debió afectar 1 registro, afectó {$total}.\n");
}

if (!empty($expedienteModel->resumenPorIds([$nuevoId]))) {
    $pdo->rollBack();
    die("ERROR: el expediente sigue apareciendo como activo tras la eliminación.\n");
}

$pdo->rollBack();
echo "ELIMINACIÓN LÓGICA MÚLTIPLE VERIFICADA (revertida al finalizar): OK\n";

echo "TODAS LAS PRUEBAS DE ELIMINACIÓN DE EXPEDIENTES PASARON CON ÉXITO.\n";
