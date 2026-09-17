<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

// Simular sesión autenticada como Jefe de Unidad Académica (Unidad 2)
\App\Core\Session::set('user', [
    'id' => 4,
    'dni' => '00000004',
    'username' => 'academica',
    'nombres' => 'Jefe',
    'apellidos' => 'Unidad Académica',
    'nombre_completo' => 'Jefe Unidad Académica',
    'email' => 'jefe.academica@tupacamaru.edu.pe',
    'cargo' => 'Jefe de Unidad Académica',
    'unidad_id' => 2,
    'debe_cambiar_password' => 0,
    'roles' => ['unidad'],
    'permissions' => ['expedientes.ver', 'expedientes.recibir', 'expedientes.responder', 'expedientes.observar']
]);

echo "1. Verificando expediente 1 antes de la recepción formal...\n";
$expModel = new \App\Models\Expediente();
$exp = $expModel->find(1);
echo "Expediente: {$exp['numero_expediente']}, Estado actual: {$exp['estado_id']}, Unidad Actual: {$exp['unidad_actual_id']}\n";

echo "2. Ejecutando recepción formal con marca de tiempo e IP...\n";
$estadoModel = new \App\Models\EstadoExpediente();
$estadoRecepcionado = $estadoModel->findByCodigo('RECEPCIONADO');
$estadoRecepcionadoId = (int)$estadoRecepcionado['id'];

\App\Core\Database::transaction(function($pdo) use ($exp, $expModel, $estadoRecepcionadoId) {
    $expModel->update(1, [
        'estado_id' => $estadoRecepcionadoId,
        'usuario_responsable_id' => 4
    ]);

    $movModel = new \App\Models\ExpedienteMovimiento();
    $movModel->record(
        1,
        'RECEPCION',
        2,
        2,
        4,
        (int)$exp['estado_id'],
        $estadoRecepcionadoId,
        'Expediente recepcionado formalmente por Jefe Unidad Académica. IP: 127.0.0.1',
        true,
        '127.0.0.1',
        'Test Runner'
    );
});

$expRecepcionado = $expModel->find(1);
echo "Nuevo Estado tras Recepción: {$expRecepcionado['estado_id']}\n";
if ((int)$expRecepcionado['estado_id'] !== $estadoRecepcionadoId) {
    die("ERROR: Fallo al actualizar estado a RECEPCIONADO.\n");
}

echo "3. Emitiendo informe técnico de atención y remitiendo a Dirección General...\n";
$estadoRespondido = $estadoModel->findByCodigo('RESPONDIDO');
$estadoRespondidoId = (int)$estadoRespondido['id'];

\App\Core\Database::transaction(function($pdo) use ($expModel, $estadoRecepcionadoId, $estadoRespondidoId) {
    $expModel->update(1, [
        'estado_id' => $estadoRespondidoId,
        'unidad_actual_id' => 1, // Dirección General
        'observacion_publica' => 'Verificación académica conforme. Certificado de estudios emitido y visado para firma directiva.'
    ]);

    $movModel = new \App\Models\ExpedienteMovimiento();
    $movModel->record(
        1,
        'RESPUESTA',
        2, // UA
        1, // DG
        4, // Jefe UA
        $estadoRecepcionadoId,
        $estadoRespondidoId,
        'Informe Académico N° 045-2026-UA: Solicitante completó 120 créditos con promedio ponderado aprobatorio. Favorable.',
        true,
        '127.0.0.1',
        'Test Runner'
    );
});

$expRespondido = $expModel->find(1);
echo "Estado final tras emitir respuesta: {$expRespondido['estado_id']}, Unidad actual: {$expRespondido['unidad_actual_id']}\n";
if ((int)$expRespondido['estado_id'] !== $estadoRespondidoId || (int)$expRespondido['unidad_actual_id'] !== 1) {
    die("ERROR: Fallo al actualizar estado a RESPONDIDO o ubicación a Dirección.\n");
}

$movs = (new \App\Models\ExpedienteMovimiento())->getHistory(1);
echo "Total de movimientos en trazabilidad inmutable: " . count($movs) . "\n";
echo "FLUJO DE ATENCIÓN DE UNIDAD Y REMISIÓN A DIRECCIÓN COMPLETADO CON ÉXITO.\n";
