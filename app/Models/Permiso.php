<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Permiso extends Model
{
    protected string $table = 'permisos';

    public function allGroupedByModule(): array
    {
        $stmt = $this->db()->prepare("SELECT * FROM `permisos` ORDER BY `modulo` ASC, `nombre` ASC");
        $stmt->execute();
        $all = $stmt->fetchAll();

        $grouped = [];
        foreach ($all as $perm) {
            $module = $perm['modulo'] ?? 'general';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $perm;
        }

        return $grouped;
    }
}
