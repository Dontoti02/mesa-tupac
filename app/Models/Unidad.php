<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Unidad extends Model
{
    protected string $table = 'unidades';

    public function allActive(): array
    {
        return $this->where('`estado` = 1', [], '`orden` ASC, `nombre` ASC');
    }
}
