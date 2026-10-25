<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['name', 'slug'])]
class Departamento extends Authenticatable
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'departamentos';

    /**
     * Get the medics that belong to this department.
     */
    public function medicos()
    {
        return $this->hasMany(Medico::class);
    }
}
