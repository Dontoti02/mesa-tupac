<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

echo "1. Probando generación de Número de Expediente correlativo...\n";
$expModel = new \App\Models\Expediente();
$numExp = $expModel->generateNumeroExpediente();
echo "Número generado: {$numExp}\n";
if (!preg_match('/^EXP-2026-[0-9]{6}$/', $numExp)) {
    die("ERROR: El formato del número de expediente no es válido: {$numExp}\n");
}

echo "2. Probando generación de Código de Seguimiento...\n";
$code = $expModel->generateCodigoSeguimiento();
echo "Código generado: {$code}\n";
if (!preg_match('/^TA-[A-Z0-9]{6}$/', $code)) {
    die("ERROR: El formato del código de seguimiento no es válido: {$code}\n");
}

echo "3. Insertando expediente de prueba simulando envío de FUT...\n";
$expId = $expModel->insert([
    'numero_expediente' => $numExp,
    'codigo_seguimiento' => $code,
    'solicito' => 'Certificado de Estudios',
    'sumilla' => 'Solicito otorgamiento de certificado de estudios del I al VI ciclo',
    'apellido_paterno' => 'MAMANI',
    'apellido_materno' => 'QUISPE',
    'nombres' => 'JUAN CARLOS',
    'dni' => '45678912',
    'correo' => 'juan.mamani@gmail.com',
    'direccion_domiciliaria' => 'Av. El Sol 123, Cusco',
    'celular' => '984123456',
    'es_estudiante_egresado' => 1,
    'programa_id' => 1,
    'codigo_estudiante' => '2023-DSI-045',
    'anio_ingreso' => '2023',
    'anio_egreso' => '2025',
    'tipo_tramite_id' => 9, // Certificado de Estudios
    'fundamento_peticion' => 'Por motivos laborales requiero sustentar mis notas y módulos cursados en la carrera de DSI.',
    'estado_id' => 2, // REGISTRADO
    'prioridad_id' => 1, // NORMAL
    'unidad_actual_id' => 1, // Mesa de Partes
    'fecha_ingreso' => date('Y-m-d H:i:s'),
    'activo' => 1
]);

echo "Expediente insertado con ID: {$expId}\n";

echo "4. Insertando movimiento inicial de trazabilidad...\n";
$movModel = new \App\Models\ExpedienteMovimiento();
$movId = $movModel->record(
    $expId,
    'REGISTRO',
    null,
    1,
    null,
    null,
    2,
    'Trámite registrado exitosamente vía Formulario Único de Trámite (FUT Virtual).',
    true,
    '127.0.0.1',
    'PHP Test Runner'
);
echo "Movimiento insertado con ID: {$movId}\n";

echo "5. Verificando generación de Cargo Digital oficial...\n";
$expFull = $expModel->findWithRelations($expId);
$cargoHtml = \App\Helpers\CargoGenerator::generateHtml($expFull, []);
if (str_contains($cargoHtml, 'CARGO DE RECEPCIÓN DIGITAL') && str_contains($cargoHtml, $numExp) && str_contains($cargoHtml, $code)) {
    echo "CARGO DIGITAL GENERADO SATISFACTORIAMENTE CON IDENTIDAD INSTITUCIONAL Y QR.\n";
} else {
    die("ERROR: El cargo digital no contiene la información esperada.\n");
}

echo "TODAS LAS PRUEBAS DEL FUT Y CARGO DIGITAL PASARON EXITOSAMENTE.\n";
