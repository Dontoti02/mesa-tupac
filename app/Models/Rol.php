<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Rol extends Model
{
    protected string $table = 'roles';

    public function getPermissions(int $rolId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT p.* 
             FROM `permisos` p 
             INNER JOIN `roles_permisos` rp ON p.id = rp.permiso_id 
             WHERE rp.rol_id = :id"
        );
        $stmt->execute(['id' => $rolId]);
        return $stmt->fetchAll();
    }

    public function syncPermissions(int $rolId, array $permissionIds): void
    {
        $this->db()->prepare("DELETE FROM `roles_permisos` WHERE `rol_id` = :id")->execute(['id' => $rolId]);
        
        if (!empty($permissionIds)) {
            $stmt = $this->db()->prepare("INSERT INTO `roles_permisos` (`rol_id`, `permiso_id`) VALUES (:rol_id, :permiso_id)");
            foreach ($permissionIds as $permId) {
                $stmt->execute(['rol_id' => $rolId, 'permiso_id' => (int)$permId]);
            }
        }
    }
}
