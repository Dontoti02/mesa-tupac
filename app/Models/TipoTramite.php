<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class TipoTramite extends Model
{
    protected string $table = 'tipos_tramite';

    public function allActiveGroupedByCategory(): array
    {
        $stmt = $this->db()->prepare(
            "SELECT t.*, c.nombre as categoria_nombre 
             FROM `tipos_tramite` t 
             INNER JOIN `categorias_tramite` c ON t.categoria_id = c.id 
             WHERE t.estado = 1 AND t.admite_virtual = 1 
             ORDER BY c.orden ASC, t.orden ASC"
        );
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $grouped = [];
        foreach ($rows as $row) {
            $cat = $row['categoria_nombre'];
            if (!isset($grouped[$cat])) {
                $grouped[$cat] = [];
            }
            $grouped[$cat][] = $row;
        }

        return $grouped;
    }

    public function allWithDetails(): array
    {
        $stmt = $this->db()->prepare(
            "SELECT t.*, c.nombre as categoria_nombre, u.nombre as unidad_nombre 
             FROM `tipos_tramite` t 
             INNER JOIN `categorias_tramite` c ON t.categoria_id = c.id 
             LEFT JOIN `unidades` u ON t.unidad_sugerida_id = u.id 
             WHERE t.estado = 1 
             ORDER BY c.orden ASC, t.orden ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
