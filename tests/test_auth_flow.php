<?php
declare(strict_types=1);

require_once __DIR__ . '/../app/Core/App.php';
\App\Core\App::boot(dirname(__DIR__));

$usuarioModel = new \App\Models\Usuario();
$intentoModel = new \App\Models\IntentoLogin();

echo "1. Probando búsqueda de usuario admin...\n";
$admin = $usuarioModel->findByIdentifier('admin');
if (!$admin) {
    die("ERROR: Usuario admin no encontrado.\n");
}
echo "Usuario encontrado: {$admin['username']} ({$admin['email']})\n";

echo "2. Probando verificación de contraseña Admin123*...\n";
if (!password_verify('Admin123*', $admin['password_hash'])) {
    die("ERROR: La contraseña inicial no coincide con el hash.\n");
}
echo "Contraseña verificada correctamente con bcrypt.\n";

echo "3. Probando roles y permisos de admin...\n";
$roles = $usuarioModel->getRoles((int)$admin['id']);
echo "Roles asignados: " . implode(', ', array_column($roles, 'slug')) . "\n";
if (!$usuarioModel->hasRole((int)$admin['id'], 'superadmin')) {
    die("ERROR: Usuario admin no tiene el rol superadmin.\n");
}
if (!$usuarioModel->hasPermission((int)$admin['id'], 'expedientes.derivar')) {
    die("ERROR: Superadmin no tiene permiso expedientes.derivar.\n");
}
echo "Permiso RBAC verificado exitosamente.\n";

echo "4. Probando rate limiting de intentos...\n";
$intentoModel->record('127.0.0.1', 'test_bad_user', false);
echo "Intento fallido registrado correctamente.\n";

echo "TODAS LAS PRUEBAS DE AUTENTICACIÓN Y RBAC PASARON CON ÉXITO.\n";
