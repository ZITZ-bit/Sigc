<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    protected $table = 'especialidades';

    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }
}

