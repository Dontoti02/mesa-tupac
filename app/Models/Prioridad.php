<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Prioridad extends Model
{
    protected string $table = 'prioridades';

    public function findByCodigo(string $codigo): ?array
    {
        return $this->findBy('codigo', $codigo);
    }
}
