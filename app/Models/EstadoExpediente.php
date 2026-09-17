<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class EstadoExpediente extends Model
{
    protected string $table = 'estados_expediente';

    public function findByCodigo(string $codigo): ?array
    {
        return $this->findBy('codigo', $codigo);
    }
}
