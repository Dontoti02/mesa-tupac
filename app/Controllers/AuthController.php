<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Session;
use App\Models\Usuario;
use App\Models\IntentoLogin;
use App\Models\Auditoria;
use App\Helpers\Validator;

class AuthController extends Controller
{
    private Usuario $usuarioModel;
    private IntentoLogin $intentoModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->usuarioModel = new Usuario();
        $this->intentoModel = new IntentoLogin();
    }

    public function showLogin(): void
    {
        if (Session::has('user')) {
            $this->redirect('/dashboard');
        }

        $this->view('auth.login', [
            'title' => 'Inicio de Sesión - Mesa de Partes Virtual'
        ], 'auth');
    }

    public function login(): void
    {
        $ip = $this->request->ip();
        $username = trim((string)$this->request->post('username', ''));
        $password = (string)$this->request->post('password', '');

        // Validación de datos
        $validator = Validator::make([
            'usuario' => $username,
            'password' => $password
        ], [
            'usuario' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            $this->redirect('/login', [
                'error' => $validator->firstOfAll(),
                'old_username' => $username
            ]);
        }

        // Control de intentos fallidos (Rate Limiting institucional)
        if ($this->intentoModel->isBlocked($ip, $username, 5, 15)) {
            Auditoria::log(null, 'LOGIN_BLOQUEADO_RATE_LIMIT', 'seguridad', null, null, [
                'username' => $username,
                'ip' => $ip
            ]);

            $this->redirect('/login', [
                'error' => 'Demasiados intentos fallidos consecutivos. Su acceso ha sido bloqueado temporalmente por 15 minutos.'
            ]);
        }

        // Búsqueda de usuario
        $user = $this->usuarioModel->findByIdentifier($username);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $this->intentoModel->record($ip, $username, false);
            
            Auditoria::log($user ? (int)$user['id'] : null, 'LOGIN_FALLIDO', 'seguridad', null, null, [
                'username' => $username,
                'ip' => $ip
            ]);

            $this->redirect('/login', [
                'error' => 'Las credenciales ingresadas son incorrectas.',
                'old_username' => $username
            ]);
        }

        // Validar si el usuario está activo
        if ((int)$user['estado'] !== 1) {
            $this->intentoModel->record($ip, $username, false);
            
            Auditoria::log((int)$user['id'], 'LOGIN_CUENTA_INACTIVA', 'seguridad');

            $this->redirect('/login', [
                'error' => 'Su cuenta institucional se encuentra inactiva o suspendida. Comuníquese con la Oficina de Personal o Administración.'
            ]);
        }

        // Login Exitoso: limpiar intentos
        $this->intentoModel->clear($ip, $username);
        $this->usuarioModel->updateLastLogin((int)$user['id']);

        // Regenerar ID de sesión para prevenir Session Fixation
        Session::regenerate(true);

        // Obtener roles y permisos para guardarlos en sesión
        $roles = $this->usuarioModel->getRoles((int)$user['id']);
        $permissions = $this->usuarioModel->getPermissions((int)$user['id']);

        // Guardar información del usuario autenticado
        Session::set('user', [
            'id' => (int)$user['id'],
            'dni' => $user['dni'],
            'username' => $user['username'],
            'nombres' => $user['nombres'],
            'apellidos' => $user['apellidos'],
            'nombre_completo' => $user['nombres'] . ' ' . $user['apellidos'],
            'email' => $user['email'],
            'cargo' => $user['cargo'],
            'unidad_id' => $user['unidad_id'] ? (int)$user['unidad_id'] : null,
            'unidad_nombre' => $user['unidad_nombre'] ?? null,
            'unidad_codigo' => $user['unidad_codigo'] ?? null,
            'debe_cambiar_password' => (int)$user['debe_cambiar_password'],
            'roles' => array_column($roles, 'slug'),
            'permissions' => $permissions
        ]);

        Auditoria::log((int)$user['id'], 'LOGIN_EXITOSO', 'auth');

        // Si tiene marcado cambio obligatorio de contraseña
        if ((int)$user['debe_cambiar_password'] === 1) {
            $this->redirect('/cambiar-password', [
                'warning' => 'Es su primer inicio de sesión o se ha restablecido su clave. Por favor cambie su contraseña.'
            ]);
        }

        $this->redirect('/dashboard', [
            'success' => 'Bienvenido al Sistema de Mesa de Partes Virtual, ' . $user['nombres'] . '.'
        ]);
    }

    public function logout(): void
    {
        $user = Session::get('user');
        if ($user) {
            Auditoria::log((int)$user['id'], 'LOGOUT', 'auth');
        }

        Session::destroy();
        $this->redirect('/login', [
            'success' => 'Ha cerrado sesión correctamente.'
        ]);
    }

    public function showChangePassword(): void
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $this->view('auth.cambiar_password', [
            'title' => 'Actualizar Contraseña - IESP Túpac Amaru'
        ], 'auth');
    }

    public function updatePassword(): void
    {
        $user = Session::get('user');
        if (!$user) {
            $this->redirect('/login');
        }

        $currentPassword = (string)$this->request->post('password_actual', '');
        $newPassword = (string)$this->request->post('password_nuevo', '');
        $confirmPassword = (string)$this->request->post('password_confirmacion', '');

        $validator = Validator::make([
            'password_actual' => $currentPassword,
            'password_nuevo' => $newPassword,
            'password_confirmacion' => $confirmPassword
        ], [
            'password_actual' => 'required',
            'password_nuevo' => 'required|min:8',
            'password_confirmacion' => 'required|same:password_nuevo'
        ]);

        if ($validator->fails()) {
            $this->redirect('/cambiar-password', [
                'error' => $validator->firstOfAll()
            ]);
        }

        // Verificar contraseña actual en base de datos
        $dbUser = $this->usuarioModel->find((int)$user['id']);
        if (!$dbUser || !password_verify($currentPassword, $dbUser['password_hash'])) {
            $this->redirect('/cambiar-password', [
                'error' => 'La contraseña actual ingresada es incorrecta.'
            ]);
        }

        // Actualizar contraseña
        $this->usuarioModel->updatePassword((int)$user['id'], $newPassword);

        // Actualizar sesión
        $user['debe_cambiar_password'] = 0;
        Session::set('user', $user);

        Auditoria::log((int)$user['id'], 'CAMBIO_PASSWORD', 'auth');

        $this->redirect('/dashboard', [
            'success' => 'Su contraseña se ha actualizado correctamente. Bienvenido a su panel.'
        ]);
    }
}
