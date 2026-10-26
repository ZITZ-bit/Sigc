<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['user_id', 'especialidad_id', 'departamento_id', 'numero_licencia', 'telefono', 'fecha_incorporacion', 'estado'])]
class Medico extends Authenticatable
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medicos';

    /**
     * Get the user that owns the medic record.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the specialty that owns the medic.
     */
    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

    /**
     * Get the department that owns the medic.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Get the invitation for this medic.
     */
    public function invitacion()
    {
        return $this->hasOne(Invitacion::class);
    }

    /**
     * Get the schedules for this medic.
     */
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }
}
