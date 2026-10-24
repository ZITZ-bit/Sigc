<?php

namespace App\Policies;

use App\Models\Consulta;
use App\Models\User;

class ConsultaPolicy
{
    public function view(User $user, Consulta $consulta): bool
    {
        if ($user->hasRole('administrador')) {
            return true;
        }
        if ($user->hasRole('paciente') && $user->paciente && $user->paciente->id === $consulta->paciente_id) {
            return true;
        }
        if ($user->hasRole('medico') && $user->medico && $user->medico->id === $consulta->medico_id) {
            return true;
        }
        return false;
    }

    public function update(User $user, Consulta $consulta): bool
    {
        if ($user->hasRole('administrador')) {
            return true;
        }
        if ($user->hasRole('medico') && $user->medico && $user->medico->id === $consulta->medico_id) {
            return true;
        }
        return false;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('paciente') || $user->hasRole('administrador');
    }
}
