<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use PDO;

class Expediente extends Model
{
    protected string $table = 'expedientes';

    public function generateNumeroExpediente(): string
    {
        $year = date('Y');
        $prefix = "EXP-{$year}-";

        $stmt = $this->db()->prepare(
            "SELECT `numero_expediente` FROM `expedientes` 
             WHERE `numero_expediente` LIKE :pattern 
             ORDER BY `id` DESC LIMIT 1"
        );
        $stmt->execute(['pattern' => "{$prefix}%"]);
        $last = $stmt->fetch();

        if ($last) {
            $lastNum = (int)substr($last['numero_expediente'], strlen($prefix));
            $nextNum = $lastNum + 1;
        } else {
            $nextNum = 1;
        }

        return $prefix . str_pad((string)$nextNum, 6, '0', STR_PAD_LEFT);
    }

    public function generateCodigoSeguimiento(): string
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $max = strlen($chars) - 1;

        do {
            $code = 'TA-';
            for ($i = 0; $i < 6; $i++) {
                $code .= $chars[random_int(0, $max)];
            }

            $stmt = $this->db()->prepare(
                "SELECT COUNT(*) as total FROM `expedientes` WHERE `codigo_seguimiento` = :code"
            );
            $stmt->execute(['code' => $code]);
            $exists = (int)$stmt->fetch()['total'] > 0;
        } while ($exists);

        return $code;
    }

    public function findWithRelations(int|string $id): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT e.*, 
                    es.nombre as estado_nombre, es.color as estado_color, es.icono as estado_icono, es.codigo as estado_codigo,
                    p.nombre as prioridad_nombre, p.color as prioridad_color,
                    t.nombre as tramite_nombre, t.codigo as tramite_codigo, t.plazo_dias, t.costo as tramite_costo,
                    c.nombre as categoria_nombre,
                    u_act.nombre as unidad_actual_nombre, u_act.codigo as unidad_actual_codigo,
                    u_resp.nombre as unidad_responsable_nombre,
                    prog.nombre as programa_nombre
             FROM `expedientes` e
             LEFT JOIN `estados_expediente` es ON e.estado_id = es.id
             LEFT JOIN `prioridades` p ON e.prioridad_id = p.id
             LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id
             LEFT JOIN `categorias_tramite` c ON t.categoria_id = c.id
             LEFT JOIN `unidades` u_act ON e.unidad_actual_id = u_act.id
             LEFT JOIN `unidades` u_resp ON e.unidad_responsable_id = u_resp.id
             LEFT JOIN `programas_estudio` prog ON e.programa_id = prog.id
             WHERE e.id = :id AND e.activo = 1
             LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function findByTracking(string $numeroExpediente, string $codigoSeguimiento, ?string $dni = null): ?array
    {
        $sql = "SELECT e.*, 
                       es.nombre as estado_nombre, es.color as estado_color, es.icono as estado_icono, es.codigo as estado_codigo,
                       t.nombre as tramite_nombre,
                       u_act.nombre as unidad_actual_nombre
                FROM `expedientes` e
                LEFT JOIN `estados_expediente` es ON e.estado_id = es.id
                LEFT JOIN `tipos_tramite` t ON e.tipo_tramite_id = t.id
                LEFT JOIN `unidades` u_act ON e.unidad_actual_id = u_act.id
                WHERE (e.numero_expediente = :num OR e.codigo_seguimiento = :code) 
                  AND e.activo = 1";
        
        $params = [
            'num' => trim($numeroExpediente),
            'code' => trim($codigoSeguimiento)
        ];

        if (!empty($dni)) {
            $sql .= " AND e.dni = :dni";
            $params['dni'] = trim($dni);
        }

        $sql .= " LIMIT 1";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute($params);
        $row = $stmt->fetch();
        return $row ?: null;
    }
}
