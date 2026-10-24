<?php

namespace App\Policies;

use App\Models\Medico;
use App\Models\User;

class MedicoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrador');
    }

    public function view(User $user, Medico $medico): bool
    {
        if ($user->hasRole('administrador')) {
            return true;
        }
        if ($user->hasRole('medico') && $user->medico && $user->medico->id === $medico->id) {
            return true;
        }
        return false;
    }

    public function update(User $user, Medico $medico): bool
    {
        if ($user->hasRole('administrador')) {
            return true;
        }
        if ($user->hasRole('medico') && $user->medico && $user->medico->id === $medico->id) {
            return true;
        }
        return false;
    }
}
