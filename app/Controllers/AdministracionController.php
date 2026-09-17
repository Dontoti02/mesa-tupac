<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Database;
use App\Models\Usuario;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Unidad;
use App\Models\ProgramaEstudio;
use App\Models\TipoTramite;
use App\Models\CategoriaTramite;
use App\Models\EstadoExpediente;
use App\Models\Auditoria;
use App\Helpers\Validator;

class AdministracionController extends Controller
{
    private Usuario $usuarioModel;
    private Rol $rolModel;
    private Permiso $permisoModel;
    private Unidad $unidadModel;
    private ProgramaEstudio $programaModel;
    private TipoTramite $tramiteModel;
    private EstadoExpediente $estadoModel;

    public function __construct(Request $request, Response $response)
    {
        parent::__construct($request, $response);
        $this->usuarioModel = new Usuario();
        $this->rolModel = new Rol();
        $this->permisoModel = new Permiso();
        $this->unidadModel = new Unidad();
        $this->programaModel = new ProgramaEstudio();
        $this->tramiteModel = new TipoTramite();
        $this->estadoModel = new EstadoExpediente();
    }

    // ==========================================
    // 1. GESTIÓN DE USUARIOS
    // ==========================================
    public function usuarios(): void
    {
        $db = Database::getConnection();
        $stmt = $db->query(
            "SELECT u.*, un.nombre as unidad_nombre, r.nombre as rol_nombre, r.slug as rol_slug 
             FROM `usuarios` u 
             LEFT JOIN `unidades` un ON u.unidad_id = un.id 
             LEFT JOIN `usuarios_roles` ur ON u.id = ur.usuario_id 
             LEFT JOIN `roles` r ON ur.rol_id = r.id 
             ORDER BY u.id ASC"
        );
        $usuarios = $stmt->fetchAll();

        $unidades = $this->unidadModel->allActive();
        $roles = $this->rolModel->all('id ASC');

        $this->view('administracion.usuarios', [
            'title' => 'Administración de Usuarios y Funcionarios',
            'usuarios' => $usuarios,
            'unidades' => $unidades,
            'roles' => $roles
        ], 'app');
    }

    public function storeUsuario(): void
    {
        $data = $this->request->all();
        $validator = Validator::make($data, [
            'dni' => 'required|dni',
            'nombres' => 'required',
            'apellidos' => 'required',
            'username' => 'required',
            'email' => 'required|email',
            'cargo' => 'required',
            'rol_id' => 'required|numeric',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            $this->redirect('/administracion/usuarios', ['error' => $validator->firstOfAll()]);
        }

        try {
            $userSession = $this->user();
            Database::transaction(function($pdo) use ($data, $userSession) {
                $hash = password_hash($data['password'], PASSWORD_BCRYPT);
                $userId = $this->usuarioModel->insert([
                    'dni' => trim((string)$data['dni']),
                    'nombres' => mb_strtoupper(trim((string)$data['nombres']), 'UTF-8'),
                    'apellidos' => mb_strtoupper(trim((string)$data['apellidos']), 'UTF-8'),
                    'username' => strtolower(trim((string)$data['username'])),
                    'email' => strtolower(trim((string)$data['email'])),
                    'password_hash' => $hash,
                    'telefono' => trim((string)($data['telefono'] ?? '')),
                    'cargo' => trim((string)$data['cargo']),
                    'unidad_id' => !empty($data['unidad_id']) ? (int)$data['unidad_id'] : null,
                    'estado' => 1,
                    'debe_cambiar_password' => 1
                ]);

                // Asignar Rol
                $stmtRol = $pdo->prepare("INSERT INTO `usuarios_roles` (`usuario_id`, `rol_id`) VALUES (:uid, :rid)");
                $stmtRol->execute(['uid' => $userId, 'rid' => (int)$data['rol_id']]);

                Auditoria::log((int)$userSession['id'], 'CREAR_USUARIO', 'usuarios', $userId, null, [
                    'username' => $data['username'],
                    'dni' => $data['dni']
                ]);
            });

            $this->redirect('/administracion/usuarios', ['success' => 'Usuario registrado exitosamente.']);
        } catch (\Throwable $e) {
            $this->redirect('/administracion/usuarios', ['error' => 'Error al registrar usuario: ' . $e->getMessage()]);
        }
    }

    public function updateUsuario(Request $request, Response $response, string $id): void
    {
        $userId = (int)$id;
        $data = $this->request->all();

        try {
            $userSession = $this->user();
            Database::transaction(function($pdo) use ($userId, $data, $userSession) {
                $updateData = [
                    'dni' => trim((string)$data['dni']),
                    'nombres' => mb_strtoupper(trim((string)$data['nombres']), 'UTF-8'),
                    'apellidos' => mb_strtoupper(trim((string)$data['apellidos']), 'UTF-8'),
                    'email' => strtolower(trim((string)$data['email'])),
                    'telefono' => trim((string)($data['telefono'] ?? '')),
                    'cargo' => trim((string)$data['cargo']),
                    'unidad_id' => !empty($data['unidad_id']) ? (int)$data['unidad_id'] : null,
                    'estado' => isset($data['estado']) ? (int)$data['estado'] : 1
                ];

                if (!empty($data['password'])) {
                    $updateData['password_hash'] = password_hash($data['password'], PASSWORD_BCRYPT);
                }

                $this->usuarioModel->update($userId, $updateData);

                if (!empty($data['rol_id'])) {
                    $pdo->prepare("DELETE FROM `usuarios_roles` WHERE `usuario_id` = :uid")->execute(['uid' => $userId]);
                    $pdo->prepare("INSERT INTO `usuarios_roles` (`usuario_id`, `rol_id`) VALUES (:uid, :rid)")
                        ->execute(['uid' => $userId, 'rid' => (int)$data['rol_id']]);
                }

                Auditoria::log((int)$userSession['id'], 'EDITAR_USUARIO', 'usuarios', $userId);
            });

            $this->redirect('/administracion/usuarios', ['success' => 'Datos de usuario actualizados correctamente.']);
        } catch (\Throwable $e) {
            $this->redirect('/administracion/usuarios', ['error' => 'Error al actualizar usuario: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 2. GESTIÓN DE ROLES Y PERMISOS
    // ==========================================
    public function roles(): void
    {
        $roles = $this->rolModel->all('id ASC');
        $permisosAgrupados = $this->permisoModel->allGroupedByModule();

        // Obtener permisos activos por rol
        $permisosPorRol = [];
        foreach ($roles as $r) {
            $permisosPorRol[$r['id']] = array_column($this->rolModel->getPermissions((int)$r['id']), 'id');
        }

        $this->view('administracion.roles', [
            'title' => 'Gestión de Roles y Matriz de Permisos RBAC',
            'roles' => $roles,
            'permisosAgrupados' => $permisosAgrupados,
            'permisosPorRol' => $permisosPorRol
        ], 'app');
    }

    public function syncRolPermisos(Request $request, Response $response, string $id): void
    {
        $rolId = (int)$id;
        $permisoIds = $this->request->post('permisos', []);

        try {
            $this->rolModel->syncPermissions($rolId, $permisoIds);
            Auditoria::log((int)$this->userId(), 'ACTUALIZAR_PERMISOS_ROL', 'roles', $rolId);
            $this->redirect('/administracion/roles', ['success' => 'Permisos del rol actualizados correctamente.']);
        } catch (\Throwable $e) {
            $this->redirect('/administracion/roles', ['error' => 'Error al sincronizar permisos: ' . $e->getMessage()]);
        }
    }

    // ==========================================
    // 3. GESTIÓN DE UNIDADES ORGÁNICAS
    // ==========================================
    public function unidades(): void
    {
        $unidades = $this->unidadModel->all('orden ASC');

        $this->view('administracion.unidades', [
            'title' => 'Catálogo de Unidades Orgánicas',
            'unidades' => $unidades
        ], 'app');
    }

    public function storeUnidad(): void
    {
        $data = $this->request->all();
        $validator = Validator::make($data, [
            'codigo' => 'required',
            'nombre' => 'required',
            'responsable' => 'required'
        ]);

        if ($validator->fails()) {
            $this->redirect('/administracion/unidades', ['error' => $validator->firstOfAll()]);
        }

        $this->unidadModel->insert([
            'codigo' => strtoupper(trim((string)$data['codigo'])),
            'nombre' => trim((string)$data['nombre']),
            'descripcion' => trim((string)($data['descripcion'] ?? '')),
            'responsable' => trim((string)$data['responsable']),
            'cargo' => trim((string)($data['cargo'] ?? '')),
            'correo' => trim((string)($data['correo'] ?? '')),
            'telefono' => trim((string)($data['telefono'] ?? '')),
            'orden' => (int)($data['orden'] ?? 0),
            'estado' => 1
        ]);

        Auditoria::log((int)$this->userId(), 'CREAR_UNIDAD', 'unidades');
        $this->redirect('/administracion/unidades', ['success' => 'Unidad orgánica registrada correctamente.']);
    }

    public function updateUnidad(Request $request, Response $response, string $id): void
    {
        $unidadId = (int)$id;
        $data = $this->request->all();

        $this->unidadModel->update($unidadId, [
            'codigo' => strtoupper(trim((string)$data['codigo'])),
            'nombre' => trim((string)$data['nombre']),
            'descripcion' => trim((string)($data['descripcion'] ?? '')),
            'responsable' => trim((string)$data['responsable']),
            'cargo' => trim((string)($data['cargo'] ?? '')),
            'correo' => trim((string)($data['correo'] ?? '')),
            'telefono' => trim((string)($data['telefono'] ?? '')),
            'orden' => (int)($data['orden'] ?? 0),
            'estado' => isset($data['estado']) ? (int)$data['estado'] : 1
        ]);

        Auditoria::log((int)$this->userId(), 'EDITAR_UNIDAD', 'unidades', $unidadId);
        $this->redirect('/administracion/unidades', ['success' => 'Datos de la unidad actualizados.']);
    }

    // ==========================================
    // 4. PROGRAMAS DE ESTUDIO
    // ==========================================
    public function programas(): void
    {
        $programas = $this->programaModel->all('orden ASC, nombre ASC');

        $this->view('administracion.programas', [
            'title' => 'Programas de Estudios Institucionales',
            'programas' => $programas
        ], 'app');
    }

    public function storePrograma(): void
    {
        $data = $this->request->all();
        $validator = Validator::make($data, [
            'codigo' => 'required',
            'nombre' => 'required'
        ]);

        if ($validator->fails()) {
            $this->redirect('/administracion/programas', ['error' => $validator->firstOfAll()]);
        }

        $this->programaModel->insert([
            'codigo' => strtoupper(trim((string)$data['codigo'])),
            'nombre' => trim((string)$data['nombre']),
            'orden' => (int)($data['orden'] ?? 0),
            'estado' => 1
        ]);

        Auditoria::log((int)$this->userId(), 'CREAR_PROGRAMA', 'programas');
        $this->redirect('/administracion/programas', ['success' => 'Programa de estudio registrado.']);
    }

    public function updatePrograma(Request $request, Response $response, string $id): void
    {
        $progId = (int)$id;
        $data = $this->request->all();

        $this->programaModel->update($progId, [
            'codigo' => strtoupper(trim((string)$data['codigo'])),
            'nombre' => trim((string)$data['nombre']),
            'orden' => (int)($data['orden'] ?? 0),
            'estado' => isset($data['estado']) ? (int)$data['estado'] : 1
        ]);

        Auditoria::log((int)$this->userId(), 'EDITAR_PROGRAMA', 'programas', $progId);
        $this->redirect('/administracion/programas', ['success' => 'Programa actualizado correctamente.']);
    }

    // ==========================================
    // 5. CATÁLOGO DE TRÁMITES FUT
    // ==========================================
    public function tramites(): void
    {
        $db = Database::getConnection();
        $stmt = $db->query(
            "SELECT t.*, c.nombre as categoria_nombre, u.nombre as unidad_sugerida_nombre 
             FROM `tipos_tramite` t 
             LEFT JOIN `categorias_tramite` c ON t.categoria_id = c.id 
             LEFT JOIN `unidades` u ON t.unidad_sugerida_id = u.id 
             ORDER BY c.orden ASC, t.orden ASC"
        );
        $tramites = $stmt->fetchAll();

        $categorias = (new CategoriaTramite())->all('orden ASC');
        $unidades = $this->unidadModel->allActive();

        $this->view('administracion.tramites', [
            'title' => 'Catálogo de Trámites del FUT',
            'tramites' => $tramites,
            'categorias' => $categorias,
            'unidades' => $unidades
        ], 'app');
    }

    public function storeTramite(): void
    {
        $data = $this->request->all();
        $validator = Validator::make($data, [
            'categoria_id' => 'required|numeric',
            'codigo' => 'required',
            'nombre' => 'required',
            'plazo_dias' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $this->redirect('/administracion/tramites', ['error' => $validator->firstOfAll()]);
        }

        $this->tramiteModel->insert([
            'categoria_id' => (int)$data['categoria_id'],
            'codigo' => trim((string)$data['codigo']),
            'nombre' => trim((string)$data['nombre']),
            'descripcion' => trim((string)($data['descripcion'] ?? '')),
            'unidad_sugerida_id' => !empty($data['unidad_sugerida_id']) ? (int)$data['unidad_sugerida_id'] : null,
            'requisitos' => trim((string)($data['requisitos'] ?? '')),
            'plazo_dias' => (int)$data['plazo_dias'],
            'costo' => (float)($data['costo'] ?? 0.00),
            'requiere_pago' => isset($data['requiere_pago']) ? 1 : 0,
            'admite_virtual' => isset($data['admite_virtual']) ? 1 : 0,
            'orden' => (int)($data['orden'] ?? 0),
            'estado' => 1
        ]);

        Auditoria::log((int)$this->userId(), 'CREAR_TIPO_TRAMITE', 'tramites');
        $this->redirect('/administracion/tramites', ['success' => 'Trámite FUT registrado exitosamente.']);
    }

    public function updateTramite(Request $request, Response $response, string $id): void
    {
        $tramiteId = (int)$id;
        $data = $this->request->all();

        $this->tramiteModel->update($tramiteId, [
            'categoria_id' => (int)$data['categoria_id'],
            'codigo' => trim((string)$data['codigo']),
            'nombre' => trim((string)$data['nombre']),
            'descripcion' => trim((string)($data['descripcion'] ?? '')),
            'unidad_sugerida_id' => !empty($data['unidad_sugerida_id']) ? (int)$data['unidad_sugerida_id'] : null,
            'requisitos' => trim((string)($data['requisitos'] ?? '')),
            'plazo_dias' => (int)$data['plazo_dias'],
            'costo' => (float)($data['costo'] ?? 0.00),
            'requiere_pago' => isset($data['requiere_pago']) ? 1 : 0,
            'admite_virtual' => isset($data['admite_virtual']) ? 1 : 0,
            'orden' => (int)($data['orden'] ?? 0),
            'estado' => isset($data['estado']) ? (int)$data['estado'] : 1
        ]);

        Auditoria::log((int)$this->userId(), 'EDITAR_TIPO_TRAMITE', 'tramites', $tramiteId);
        $this->redirect('/administracion/tramites', ['success' => 'Trámite FUT actualizado.']);
    }

    // ==========================================
    // 6. ESTADOS DE EXPEDIENTE
    // ==========================================
    public function estados(): void
    {
        $estados = $this->estadoModel->all('orden ASC');

        $this->view('administracion.estados', [
            'title' => 'Estados Normativos de Expedientes',
            'estados' => $estados
        ], 'app');
    }

    public function updateEstado(Request $request, Response $response, string $id): void
    {
        $estadoId = (int)$id;
        $data = $this->request->all();

        $this->estadoModel->update($estadoId, [
            'nombre' => trim((string)$data['nombre']),
            'color' => trim((string)$data['color']),
            'icono' => trim((string)$data['icono']),
            'es_publico' => isset($data['es_publico']) ? 1 : 0,
            'activo' => isset($data['activo']) ? 1 : 0
        ]);

        Auditoria::log((int)$this->userId(), 'EDITAR_ESTADO_EXPEDIENTE', 'estados', $estadoId);
        $this->redirect('/administracion/estados', ['success' => 'Estado actualizado correctamente.']);
    }
}
