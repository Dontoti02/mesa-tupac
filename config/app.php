<?php
declare(strict_types=1);

return [
    'name' => getenv('APP_NAME') ?: 'Mesa de Partes - IESP Tupac Amaru',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim(getenv('APP_URL') ?: 'http://localhost/mesa-tupac', '/'),
    'timezone' => getenv('APP_TIMEZONE') ?: 'America/Lima',
];
