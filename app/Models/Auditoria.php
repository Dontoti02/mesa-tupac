<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Auditoria extends Model
{
    protected string $table = 'auditoria';

    public static function log(
        ?int $usuarioId,
        string $accion,
        string $modulo,
        ?int $registroId = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): void {
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = $userAgent ?? substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido', 0, 255);

        $instance = new self();
        $stmt = $instance->db()->prepare(
            "INSERT INTO `auditoria` 
             (`usuario_id`, `accion`, `modulo`, `registro_id`, `ip`, `user_agent`, `datos_anteriores`, `datos_nuevos`, `created_at`) 
             VALUES (:uid, :accion, :modulo, :regid, :ip, :ua, :prev, :next, NOW())"
        );

        $stmt->execute([
            'uid' => $usuarioId,
            'accion' => $accion,
            'modulo' => $modulo,
            'regid' => $registroId,
            'ip' => $ip,
            'ua' => $userAgent,
            'prev' => $datosAnteriores ? json_encode($datosAnteriores, JSON_UNESCAPED_UNICODE) : null,
            'next' => $datosNuevos ? json_encode($datosNuevos, JSON_UNESCAPED_UNICODE) : null
        ]);
    }

    public function getLatest(int $limit = 50, array $filters = []): array
    {
        $sql = "SELECT a.*, u.username, u.nombres, u.apellidos, u.cargo 
                FROM `auditoria` a 
                LEFT JOIN `usuarios` u ON a.usuario_id = u.id 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['modulo'])) {
            $sql .= " AND a.modulo = :modulo";
            $params['modulo'] = $filters['modulo'];
        }
        if (!empty($filters['accion'])) {
            $sql .= " AND a.accion = :accion";
            $params['accion'] = $filters['accion'];
        }
        if (!empty($filters['usuario_id'])) {
            $sql .= " AND a.usuario_id = :usuario_id";
            $params['usuario_id'] = $filters['usuario_id'];
        }
        if (!empty($filters['fecha_desde'])) {
            $sql .= " AND DATE(a.created_at) >= :fecha_desde";
            $params['fecha_desde'] = $filters['fecha_desde'];
        }
        if (!empty($filters['fecha_hasta'])) {
            $sql .= " AND DATE(a.created_at) <= :fecha_hasta";
            $params['fecha_hasta'] = $filters['fecha_hasta'];
        }

        $sql .= " ORDER BY a.created_at DESC LIMIT {$limit}";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
