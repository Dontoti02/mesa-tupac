<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Notificacion extends Model
{
    protected string $table = 'notificaciones';

    public static function send(
        ?int $usuarioId,
        ?int $unidadId,
        string $titulo,
        string $mensaje,
        ?string $url = null,
        string $tipo = 'info'
    ): int {
        $model = new self();
        return $model->insert([
            'usuario_id' => $usuarioId,
            'unidad_id' => $unidadId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'url' => $url,
            'leido' => 0,
            'tipo' => $tipo
        ]);
    }

    public function getUnreadCount(int $usuarioId, ?int $unidadId = null): int
    {
        $sql = "SELECT COUNT(*) as total FROM `notificaciones` 
                WHERE (`usuario_id` = :uid";
        $params = ['uid' => $usuarioId];

        if ($unidadId !== null) {
            $sql .= " OR `unidad_id` = :unid";
            $params['unid'] = $unidadId;
        }

        $sql .= ") AND `leido` = 0";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return (int)($stmt->fetch()['total'] ?? 0);
    }

    public function getForUser(int $usuarioId, ?int $unidadId = null, int $limit = 20): array
    {
        $sql = "SELECT * FROM `notificaciones` 
                WHERE (`usuario_id` = :uid";
        $params = ['uid' => $usuarioId];

        if ($unidadId !== null) {
            $sql .= " OR `unidad_id` = :unid";
            $params['unid'] = $unidadId;
        }

        $sql .= ") ORDER BY `created_at` DESC LIMIT {$limit}";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function markAllAsRead(int $usuarioId, ?int $unidadId = null): bool
    {
        $sql = "UPDATE `notificaciones` SET `leido` = 1 
                WHERE (`usuario_id` = :uid";
        $params = ['uid' => $usuarioId];

        if ($unidadId !== null) {
            $sql .= " OR `unidad_id` = :unid";
            $params['unid'] = $unidadId;
        }

        $sql .= ") AND `leido` = 0";

        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($params);
    }
}
