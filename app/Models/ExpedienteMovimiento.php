<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ExpedienteMovimiento extends Model
{
    protected string $table = 'expediente_movimientos';

    public function record(
        int $expedienteId,
        string $tipoMovimiento,
        ?int $origenId,
        ?int $destinoId,
        ?int $usuarioId,
        ?int $estadoAnteriorId,
        int $estadoNuevoId,
        ?string $observacion = null,
        bool $esPublico = true,
        ?string $ip = null,
        ?string $userAgent = null
    ): int {
        $ip = $ip ?? ($_SERVER['REMOTE_ADDR'] ?? '127.0.0.1');
        $userAgent = $userAgent ?? substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido', 0, 255);

        return $this->insert([
            'expediente_id' => $expedienteId,
            'tipo_movimiento' => $tipoMovimiento,
            'unidad_origen_id' => $origenId,
            'unidad_destino_id' => $destinoId,
            'usuario_id' => $usuarioId,
            'estado_anterior_id' => $estadoAnteriorId,
            'estado_nuevo_id' => $estadoNuevoId,
            'observacion' => $observacion,
            'es_publico' => $esPublico ? 1 : 0,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);
    }

    public function getHistory(int $expedienteId, bool $onlyPublic = false): array
    {
        $sql = "SELECT m.*, 
                       u_orig.nombre as unidad_origen_nombre,
                       u_dest.nombre as unidad_destino_nombre,
                       usr.nombres as usuario_nombres, usr.apellidos as usuario_apellidos,
                       ea.nombre as estado_anterior_nombre, ea.color as estado_anterior_color,
                       en.nombre as estado_nuevo_nombre, en.color as estado_nuevo_color, en.icono as estado_nuevo_icono
                FROM `expediente_movimientos` m
                LEFT JOIN `unidades` u_orig ON m.unidad_origen_id = u_orig.id
                LEFT JOIN `unidades` u_dest ON m.unidad_destino_id = u_dest.id
                LEFT JOIN `usuarios` usr ON m.usuario_id = usr.id
                LEFT JOIN `estados_expediente` ea ON m.estado_anterior_id = ea.id
                LEFT JOIN `estados_expediente` en ON m.estado_nuevo_id = en.id
                WHERE m.expediente_id = :id";

        if ($onlyPublic) {
            $sql .= " AND m.es_publico = 1";
        }

        $sql .= " ORDER BY m.created_at ASC, m.id ASC";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['id' => $expedienteId]);
        return $stmt->fetchAll();
    }
}
