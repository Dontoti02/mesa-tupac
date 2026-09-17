<?php
declare(strict_types=1);

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/health';
$_SERVER['SCRIPT_NAME'] = '/index.php';

require_once __DIR__ . '/../public/index.php';
