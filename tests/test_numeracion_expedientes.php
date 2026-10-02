<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

use App\Core\Database;
use App\Models\Configuracion;
use App\Models\Expediente;

$fallos = 0;

function verificar(string $titulo, bool $ok, string $detalle = ''): void
{
    global $fallos;
    echo ($ok ? "   [OK]   " : "   [FALLA] ") . $titulo . ($detalle !== '' ? " -> {$detalle}" : '') . "\n";
    if (!$ok) {
        $fallos++;
    }
}

$pdo = Database::getConnection();
$modelo = new Expediente();
$anio = date('Y');

// Partir siempre de la configuración institucional por defecto
Configuracion::setMultiple([
    'expediente_num_sigla' => 'EXP',
    'expediente_num_incluir_anio' => '1',
    'expediente_num_digitos' => '6',
    'expediente_num_inicio' => '1'
]);

// 1. Configuración vigente por defecto
echo "1. Configuración por defecto (EXP-{$anio}-000000)\n";
$numeracion = Expediente::getNumeracionSettings();
verificar('Sigla por defecto EXP', $numeracion['sigla'] === 'EXP', $numeracion['sigla']);
verificar('Correlativo reinicia por año', $numeracion['clave_correlativo'] === "EXPEDIENTE-{$anio}", $numeracion['clave_correlativo']);
verificar('La vista previa no consume correlativo', Expediente::previewNumeroExpediente() === Expediente::previewNumeroExpediente());

// 2. Atomicidad: dos reservas seguidas y reversión de la transacción
echo "2. Reserva atómica del correlativo\n";
$pdo->beginTransaction();
$primero = $modelo->generateNumeroExpediente();
$segundo = $modelo->generateNumeroExpediente();
verificar('Numeración consecutiva sin saltos', $primero !== $segundo, "{$primero} / {$segundo}");
$pdo->rollBack();
verificar('La reversión devuelve el número', Expediente::previewNumeroExpediente() === $primero, Expediente::previewNumeroExpediente());

// 3. Sigla y cantidad de dígitos configurables
echo "3. Sigla y cantidad de dígitos configurables\n";
Configuracion::setMultiple([
    'expediente_num_sigla' => 'MP-TA',
    'expediente_num_digitos' => '4'
]);
verificar(
    'Formato con sigla MP-TA y 4 dígitos',
    (bool)preg_match('/^MP-TA-' . $anio . '-\d{4,}$/', Expediente::previewNumeroExpediente()),
    Expediente::previewNumeroExpediente()
);

// 4. Correlativo global cuando el año no forma parte del número
echo "4. Correlativo único sin año\n";
Configuracion::setMultiple([
    'expediente_num_sigla' => 'EXP',
    'expediente_num_incluir_anio' => '0',
    'expediente_num_digitos' => '6'
]);
verificar('Clave global del correlativo', Expediente::getNumeracionSettings()['clave_correlativo'] === 'EXPEDIENTE-GLOBAL');
verificar('Formato sin año', (bool)preg_match('/^EXP-\d{6,}$/', Expediente::previewNumeroExpediente()), Expediente::previewNumeroExpediente());

// 5. Número inicial en un correlativo nuevo
echo "5. Número inicial configurado\n";
Configuracion::setMultiple([
    'expediente_num_incluir_anio' => '1',
    'expediente_num_inicio' => '5000'
]);
$pdo->prepare("DELETE FROM `correlativos` WHERE `clave` = ?")->execute(["EXPEDIENTE-{$anio}"]);
verificar('Arranca en el número inicial', Expediente::previewNumeroExpediente() === "EXP-{$anio}-005000", Expediente::previewNumeroExpediente());

// 6. Valores corruptos caen a valores seguros
echo "6. Valores no permitidos\n";
Configuracion::setMultiple([
    'expediente_num_sigla' => '###',
    'expediente_num_digitos' => '99',
    'expediente_num_inicio' => '-3'
]);
$numeracion = Expediente::getNumeracionSettings();
verificar('Siglainválida -> EXP', $numeracion['sigla'] === 'EXP', $numeracion['sigla']);
verificar('Dígitos fuera de rango -> 6', $numeracion['digitos'] === 6, (string)$numeracion['digitos']);
verificar('Inicio fuera de rango -> 1', $numeracion['inicio'] === 1, (string)$numeracion['inicio']);

// 7. El código de seguimiento aleatorio no se ve afectado
echo "7. Código de seguimiento\n";
verificar(
    'Formato TA-XXXXXX intacto',
    (bool)preg_match('/^TA-[A-Z0-9]{6}$/', $modelo->generateCodigoSeguimiento())
);

// Restaurar la configuración institucional por defecto y la secuencia del año
Configuracion::setMultiple([
    'expediente_num_sigla' => 'EXP',
    'expediente_num_incluir_anio' => '1',
    'expediente_num_digitos' => '6',
    'expediente_num_inicio' => '1'
]);
$pdo->exec("DELETE FROM `correlativos`");
$pdo->exec(
    "INSERT INTO `correlativos` (`clave`, `ultimo_numero`)
     SELECT CONCAT('EXPEDIENTE-', YEAR(CURDATE())),
            COALESCE(MAX(CAST(RIGHT(`numero_expediente`, 6) AS UNSIGNED)), 0)
     FROM `expedientes`
     WHERE `numero_expediente` LIKE CONCAT('EXP-', YEAR(CURDATE()), '-%')"
);
echo "Configuración restaurada. Próximo número: " . Expediente::previewNumeroExpediente() . "\n";

if ($fallos > 0) {
    echo "{$fallos} PRUEBAS FALLARON\n";
    exit(1);
}

echo "TODAS LAS PRUEBAS DE NUMERACIÓN DE EXPEDIENTES PASARON EXITOSAMENTE.\n";