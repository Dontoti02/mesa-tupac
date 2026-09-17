<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ExpedienteDocumento extends Model
{
    protected string $table = 'expediente_documentos';

    public function getByExpediente(int $expedienteId): array
    {
        $stmt = $this->db()->prepare(
            "SELECT d.*, u.nombres, u.apellidos 
             FROM `expediente_documentos` d 
             LEFT JOIN `usuarios` u ON d.usuario_id = u.id 
             WHERE d.expediente_id = :id 
             ORDER BY d.created_at ASC"
        );
        $stmt->execute(['id' => $expedienteId]);
        return $stmt->fetchAll();
    }
}
