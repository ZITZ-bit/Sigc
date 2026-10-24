<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Clinica extends Model
{
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo',
    ];

    public function medicos(): HasMany
    {
        return $this->hasMany(Medico::class);
    }

    public function consultas(): HasMany
    {
        return $this->hasMany(Consulta::class);
    }

    public function invites(): HasMany
    {
        return $this->hasMany(Invite::class);
    }
}
