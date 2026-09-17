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
use App\Models\PasswordReset;
use App\Models\Configuracion;
use App\Helpers\Validator;
use App\Helpers\Mailer;

class AuthController extends Controller
{
    private Usuario $usuarioModel;
    private IntentoLogin $intentoModel;
    private PasswordReset $resetModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->usuarioModel = new Usuario();
        $this->intentoModel = new IntentoLogin();
        $this->resetModel = new PasswordReset();
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

    public function showForgotPassword(): void
    {
        if (Session::has('user')) {
            $this->redirect('/dashboard');
        }

        $smtpActivo = Mailer::isActive();
        $config = Configuracion::getAll();

        $this->view('auth.recuperar_password', [
            'title' => 'Recuperación de Contraseña - Mesa de Partes Virtual',
            'smtpActivo' => $smtpActivo,
            'config' => $config
        ], 'auth');
    }

    public function sendResetLink(): void
    {
        if (Session::has('user')) {
            $this->redirect('/dashboard');
            return;
        }

        if (!Mailer::isActive()) {
            $this->redirect('/recuperar-password', [
                'error' => 'El servicio automatizado de recuperación por correo electrónico se encuentra desactivado. Por favor contacte con soporte técnico.'
            ]);
            return;
        }

        $email = trim((string)$this->request->post('email', ''));

        $validator = Validator::make([
            'email' => $email
        ], [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            $this->redirect('/recuperar-password', [
                'error' => $validator->firstOfAll(),
                'old_email' => $email
            ]);
            return;
        }

        $user = $this->usuarioModel->findByEmail($email);

        if ($user && !empty($user['estado'])) {
            $token = $this->resetModel->createToken($email);

            $configApp = require __DIR__ . '/../../config/app.php';
            $baseUrl = rtrim($configApp['url'], '/');
            $resetUrl = $baseUrl . '/restablecer-password?token=' . urlencode($token);

            $instConfig = Configuracion::getAll();
            $instNombre = $instConfig['institucion_nombre'] ?? 'INSTITUTO DE EDUCACIÓN SUPERIOR PÚBLICO TÚPAC AMARU – CUSCO';
            $instNombreCorto = $instConfig['institucion_nombre_corto'] ?? 'IESP Túpac Amaru';
            $instEmail = $instConfig['institucion_email'] ?? 'mesadepartes@tupacamaru.edu.pe';
            $nombreUsuario = trim($user['nombres'] . ' ' . $user['apellidos']);

            $subject = "Recuperación de Contraseña - Mesa de Partes Virtual";
            $html = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #D1D5DB; border-radius: 8px; overflow: hidden; background: #ffffff;'>
                <div style='background: linear-gradient(135deg, #B3261E 0%, #7F1D1D 100%); color: #FFFFFF; padding: 24px; text-align: center; border-bottom: 4px solid #F97316;'>
                    <h2 style='margin: 0; font-size: 20px; font-weight: bold; text-transform: uppercase;'>Mesa de Partes Virtual</h2>
                    <p style='margin: 6px 0 0 0; font-size: 13px; opacity: 0.9;'>{$instNombreCorto}</p>
                </div>
                <div style='padding: 28px 24px; color: #1F2937; line-height: 1.6;'>
                    <h3 style='margin-top: 0; color: #111827; font-size: 18px;'>Estimado(a) {$nombreUsuario},</h3>
                    <p style='color: #4B5563;'>Hemos recibido una solicitud para restablecer la contraseña de acceso a su cuenta institucional en el sistema de Trámite Documentario Digital.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetUrl}' style='display: inline-block; background-color: #B3261E; color: #ffffff; text-decoration: none; padding: 12px 28px; font-weight: bold; border-radius: 6px; font-size: 15px; box-shadow: 0 4px 6px -1px rgba(179,38,30,0.3);'>
                            Restablecer mi Contraseña
                        </a>
                    </div>
                    
                    <div style='background: #FFFBEB; border-left: 4px solid #F59E0B; padding: 14px; margin: 20px 0; border-radius: 4px;'>
                        <p style='margin: 0; font-size: 13px; color: #92400E;'>
                            <strong>Importante:</strong> Este enlace es de un solo uso y expirará automáticamente en <strong>60 minutos</strong>.
                        </p>
                    </div>

                    <p style='font-size: 13px; color: #6B7280; margin-bottom: 5px;'>Si tiene inconvenientes con el botón, copie y pegue el siguiente enlace en su navegador:</p>
                    <p style='font-size: 12px; color: #3B82F6; word-break: break-all; margin-top: 0;'>{$resetUrl}</p>

                    <hr style='border: none; border-top: 1px solid #E5E7EB; margin: 24px 0;'>
                    <p style='font-size: 12px; color: #9CA3AF; margin: 0;'>
                        Si usted no realizó esta solicitud, puede desestimar este correo. Su contraseña actual permanece inalterada y segura.
                    </p>
                </div>
                <div style='background: #F3F4F6; padding: 14px; text-align: center; color: #6B7280; font-size: 12px; border-top: 1px solid #E5E7EB;'>
                    <strong>{$instNombre}</strong><br>
                    Mesa de Partes Virtual &bull; Soporte: {$instEmail}
                </div>
            </div>
            ";

            Mailer::send($email, $subject, $html);
            Auditoria::log((int)$user['id'], 'SOLICITUD_RECUPERACION_PASSWORD', 'auth', null, null, ['email' => $email]);
        }

        $this->redirect('/recuperar-password', [
            'success' => 'Si el correo electrónico ingresado coincide con una cuenta institucional registrada y activa, se ha enviado un enlace seguro de recuperación. Por favor revise su bandeja de entrada o correo no deseado (spam).'
        ]);
        return;
    }

    public function showResetPassword(): void
    {
        if (Session::has('user')) {
            $this->redirect('/dashboard');
            return;
        }

        $token = trim((string)$this->request->get('token', ''));

        if (empty($token)) {
            $this->redirect('/login', [
                'error' => 'El enlace de recuperación no es válido o ha caducado.'
            ]);
            return;
        }

        $reset = $this->resetModel->findValidToken($token);

        if (!$reset) {
            $this->redirect('/recuperar-password', [
                'error' => 'El enlace de recuperación no es válido o ya ha expirado (validez máxima de 60 minutos). Por favor solicite uno nuevo.'
            ]);
            return;
        }

        $this->view('auth.restablecer_password', [
            'title' => 'Establecer Nueva Contraseña - Mesa de Partes Virtual',
            'token' => $token,
            'email' => $reset['email']
        ], 'auth');
    }

    public function resetPassword(): void
    {
        if (Session::has('user')) {
            $this->redirect('/dashboard');
            return;
        }

        $token = trim((string)$this->request->post('token', ''));
        $password = (string)$this->request->post('password', '');
        $passwordConfirm = (string)$this->request->post('password_confirmacion', '');

        $validator = Validator::make([
            'token' => $token,
            'password' => $password,
            'password_confirmacion' => $passwordConfirm
        ], [
            'token' => 'required',
            'password' => 'required|min:8',
            'password_confirmacion' => 'required|same:password'
        ]);

        if ($validator->fails()) {
            $this->redirect('/restablecer-password?token=' . urlencode($token), [
                'error' => $validator->firstOfAll()
            ]);
            return;
        }

        $reset = $this->resetModel->findValidToken($token);

        if (!$reset) {
            $this->redirect('/recuperar-password', [
                'error' => 'El enlace de recuperación ya no es válido o ha expirado. Por favor, solicite un nuevo enlace.'
            ]);
            return;
        }

        $user = $this->usuarioModel->findByEmail($reset['email']);

        if (!$user) {
            $this->redirect('/login', [
                'error' => 'No se encontró la cuenta de usuario asociada a este enlace.'
            ]);
            return;
        }

        // Actualizar contraseña
        $this->usuarioModel->updatePassword((int)$user['id'], $password);

        // Marcar token como utilizado
        $this->resetModel->markUsed($token);

        // Registro en auditoría
        Auditoria::log((int)$user['id'], 'RECUPERACION_PASSWORD_EXITOSA', 'auth', null, null, [
            'email' => $reset['email'],
            'ip' => $this->request->ip()
        ]);

        $this->redirect('/login', [
            'success' => '¡Su contraseña ha sido restablecida exitosamente! Ya puede ingresar al sistema con su nueva clave institucional.'
        ]);
        return;
    }
}

