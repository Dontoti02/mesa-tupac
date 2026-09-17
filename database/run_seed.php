<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

$pdo = \App\Core\Database::getConnection();
$sql = file_get_contents(__DIR__ . '/seed.sql');

echo "Ejecutando seed.sql en UTF-8 nativo vía PDO...\n";
$pdo->exec($sql);
echo "Seed ejecutado exitosamente.\n";
