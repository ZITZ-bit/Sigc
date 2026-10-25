<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

#[Fillable(['medico_id', 'email', 'token', 'token_hash', 'expires_at', 'used_at'])]
class Invitacion extends Authenticatable
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'invitaciones';

    /**
     * Get the medic that owns the invitation.
     */
    public function medico()
    {
        return $this->belongsTo(Medico::class);
    }
}
