<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class ProgramaEstudio extends Model
{
    protected string $table = 'programas_estudio';

    public function allActive(): array
    {
        return $this->where('`estado` = 1', [], '`orden` ASC, `nombre` ASC');
    }
}
