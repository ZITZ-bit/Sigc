<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['fecha', 'hora', 'motivo'])]
class Consulta extends Authenticatable
{
    use HasFactory;

    /**
     * Get the user that owns the consultation request.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the medic assigned to the consultation.
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }

    /**
     * Get the specialty of the consultation.
     */
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }
}
