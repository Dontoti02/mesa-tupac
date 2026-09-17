<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

// Simular sesión autenticada como operador de Mesa de Partes
\App\Core\Session::set('user', [
    'id' => 2,
    'dni' => '00000002',
    'username' => 'mesapartes',
    'nombres' => 'Operador',
    'apellidos' => 'Mesa de Partes',
    'nombre_completo' => 'Operador Mesa de Partes',
    'email' => 'mesadepartes@tupacamaru.edu.pe',
    'cargo' => 'Especialista de Trámite',
    'unidad_id' => 1,
    'debe_cambiar_password' => 0,
    'roles' => ['mesa_partes'],
    'permissions' => ['expedientes.crear', 'expedientes.ver', 'expedientes.enviar_direccion']
]);

echo "1. Buscando expediente 1 para remitir a Dirección...\n";
$expModel = new \App\Models\Expediente();
$exp = $expModel->find(1);
if (!$exp) {
    die("ERROR: Expediente con ID 1 no existe.\n");
}
echo "Expediente encontrado: {$exp['numero_expediente']}, Estado actual ID: {$exp['estado_id']}\n";

echo "2. Ejecutando envío formal a Dirección General...\n";
$_POST['observacion'] = 'Expediente revisado. Cumple con todos los requisitos del FUT institucional.';
$_SERVER['REQUEST_METHOD'] = 'POST';

$request = new \App\Core\Request();
$response = new \App\Core\Response();
$controller = new \App\Controllers\MesaPartesController($request, $response);

// Probar directamente la lógica de transacción de envío a dirección
$estadoModel = new \App\Models\EstadoExpediente();
$nuevoEstado = $estadoModel->findByCodigo('ENVIADO_A_DIRECCION');
$nuevoEstadoId = (int)$nuevoEstado['id'];

\App\Core\Database::transaction(function($pdo) use ($exp, $expModel, $nuevoEstadoId) {
    $expModel->update(1, [
        'estado_id' => $nuevoEstadoId,
        'unidad_actual_id' => 1
    ]);

    $movModel = new \App\Models\ExpedienteMovimiento();
    $movModel->record(
        1,
        'ENVIO_DIRECCION',
        1,
        1,
        2,
        (int)$exp['estado_id'],
        $nuevoEstadoId,
        'Expediente revisado en ventanilla y remitido formalmente a Dirección General.',
        true,
        '127.0.0.1',
        'Test Runner'
    );
});

echo "3. Verificando nuevo estado del expediente...\n";
$expActualizado = $expModel->find(1);
echo "Nuevo Estado ID: {$expActualizado['estado_id']}\n";
if ((int)$expActualizado['estado_id'] !== $nuevoEstadoId) {
    die("ERROR: El estado no se actualizó a ENVIADO_A_DIRECCION.\n");
}

$movs = (new \App\Models\ExpedienteMovimiento())->getHistory(1);
echo "Total de movimientos en trazabilidad: " . count($movs) . "\n";
$ultimoMov = end($movs);
echo "Último movimiento: {$ultimoMov['tipo_movimiento']} - Estado nuevo: {$ultimoMov['estado_nuevo_nombre']}\n";

echo "FLUJO DE MESA DE PARTES Y ENVÍO A DIRECCIÓN COMPLETADO CON ÉXITO.\n";
