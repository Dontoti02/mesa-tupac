<?php
declare(strict_types=1);

/**
 * Mesa de Partes Virtual - IESP Túpac Amaru Cusco
 * Front Controller Único
 */

require_once __DIR__ . '/../app/Core/App.php';

\App\Core\App::run(dirname(__DIR__));
