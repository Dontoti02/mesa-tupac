<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

// Simular sesión autenticada como Director General
\App\Core\Session::set('user', [
    'id' => 3,
    'dni' => '00000003',
    'username' => 'director',
    'nombres' => 'Director',
    'apellidos' => 'Institucional',
    'nombre_completo' => 'Director General',
    'email' => 'director@tupacamaru.edu.pe',
    'cargo' => 'Director General',
    'unidad_id' => 1,
    'debe_cambiar_password' => 0,
    'roles' => ['direccion'],
    'permissions' => ['expedientes.ver', 'expedientes.derivar', 'expedientes.observar', 'expedientes.aprobar']
]);

echo "1. Verificando expediente 1 antes de derivar...\n";
$expModel = new \App\Models\Expediente();
$exp = $expModel->find(1);
echo "Expediente: {$exp['numero_expediente']}, Estado ID actual: {$exp['estado_id']}\n";

echo "2. Derivando a Unidad Académica (Unidad ID: 2)...\n";
$estadoModel = new \App\Models\EstadoExpediente();
$estadoDerivado = $estadoModel->findByCodigo('DERIVADO');
$estadoDerivadoId = (int)$estadoDerivado['id'];

\App\Core\Database::transaction(function($pdo) use ($exp, $expModel, $estadoDerivadoId) {
    $expModel->update(1, [
        'estado_id' => $estadoDerivadoId,
        'unidad_actual_id' => 2, // Unidad Académica
        'unidad_responsable_id' => 2,
        'prioridad_id' => 2 // URGENTE
    ]);

    $movModel = new \App\Models\ExpedienteMovimiento();
    $movModel->record(
        1,
        'DERIVACION',
        1, // Dirección
        2, // Unidad Académica
        3, // Director
        (int)$exp['estado_id'],
        $estadoDerivadoId,
        'Pase a Unidad Académica para informe y emisión de certificado de estudios.',
        true,
        '127.0.0.1',
        'Test Runner'
    );
});

echo "3. Verificando actualización del expediente derivado...\n";
$expActualizado = $expModel->find(1);
echo "Nuevo Estado: {$expActualizado['estado_id']}, Unidad Actual: {$expActualizado['unidad_actual_id']}\n";
if ((int)$expActualizado['estado_id'] !== $estadoDerivadoId || (int)$expActualizado['unidad_actual_id'] !== 2) {
    die("ERROR: Fallo al verificar derivación del expediente.\n");
}

$movs = (new \App\Models\ExpedienteMovimiento())->getHistory(1);
echo "Total de movimientos en trazabilidad: " . count($movs) . "\n";
$ultimo = end($movs);
echo "Último movimiento registrado: {$ultimo['tipo_movimiento']} de {$ultimo['unidad_origen_nombre']} hacia {$ultimo['unidad_destino_nombre']}\n";

echo "FLUJO DE DERIVACIÓN DESDE DIRECCIÓN GENERAL VERIFICADO CON ÉXITO.\n";
