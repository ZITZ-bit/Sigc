<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['medico_id', 'dia_semana', 'hora_inicio', 'hora_fin', 'activo'])]
class Horario extends Authenticatable
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'horarios';

    /**
     * Get the medic that owns this schedule.
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
