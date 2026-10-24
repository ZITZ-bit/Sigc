<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nota extends Model
{
    protected $fillable = [
        'consulta_id',
        'medico_id',
        'paciente_id',
        'contenido',
        'diagnostico',
        'tratamiento',
        'firmada',
        'firmada_at',
    ];

    protected function casts(): array
    {
        return [
            'firmada' => 'boolean',
            'firmada_at' => 'datetime',
        ];
    }

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(Consulta::class);
    }

    public function medico(): BelongsTo
    {
        return $this->belongsTo(Medico::class);
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }
}
