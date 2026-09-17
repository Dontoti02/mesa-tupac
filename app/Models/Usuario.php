<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Usuario extends Model
{
    protected string $table = 'usuarios';

    public function findByIdentifier(string $identifier): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT u.*, un.nombre AS unidad_nombre, un.codigo AS unidad_codigo 
             FROM `usuarios` u 
             LEFT JOIN `unidades` un ON u.unidad_id = un.id 
             WHERE u.username = :u_ident OR u.email = :e_ident OR u.dni = :d_ident 
             LIMIT 1"
        );
        $stmt->execute([
            'u_ident' => $identifier,
            'e_ident' => $identifier,
            'd_ident' => $identifier
        ]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getRoles(int $usuarioId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT r.* 
             FROM `roles` r 
             INNER JOIN `usuarios_roles` ur ON r.id = ur.rol_id 
             WHERE ur.usuario_id = :id AND r.estado = 1"
        );
        $stmt->execute(['id' => $usuarioId]);
        return $stmt->fetchAll();
    }

    public function getPermissions(int $usuarioId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT DISTINCT p.slug 
             FROM `permisos` p 
             INNER JOIN `roles_permisos` rp ON p.id = rp.permiso_id 
             INNER JOIN `usuarios_roles` ur ON rp.rol_id = ur.rol_id 
             WHERE ur.usuario_id = :id"
        );
        $stmt->execute(['id' => $usuarioId]);
        return array_column($stmt->fetchAll(), 'slug');
    }

    public function hasRole(int $usuarioId, string $roleSlug): bool
    {
        $roles = $this->getRoles($usuarioId);
        foreach ($roles as $role) {
            if ($role['slug'] === $roleSlug) {
                return true;
            }
        }
        return false;
    }

    public function hasPermission(int $usuarioId, string $permissionSlug): bool
    {
        // El rol 'superadmin' tiene automáticamente todos los permisos
        if ($this->hasRole($usuarioId, 'superadmin')) {
            return true;
        }

        $permissions = $this->getPermissions($usuarioId);
        return in_array($permissionSlug, $permissions, true);
    }

    public function updateLastLogin(int $usuarioId): void
    {
        $stmt = $this->db()->prepare(
            "UPDATE `usuarios` SET `ultimo_acceso` = NOW() WHERE `id` = :id"
        );
        $stmt->execute(['id' => $usuarioId]);
    }

    public function updatePassword(int $usuarioId, string $newPassword): bool
    {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->db()->prepare(
            "UPDATE `usuarios` SET `password_hash` = :hash, `debe_cambiar_password` = 0 WHERE `id` = :id"
        );
        return $stmt->execute([
            'hash' => $hash,
            'id' => $usuarioId
        ]);
    }
}
