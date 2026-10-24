<?php

namespace App\Policies;

use App\Models\Paciente;
use App\Models\User;

class PacientePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('administrador');
    }

    public function view(User $user, Paciente $paciente): bool
    {
        if ($user->hasRole('administrador')) {
            return true;
        }
        if ($user->hasRole('paciente') && $user->paciente && $user->paciente->id === $paciente->id) {
            return true;
        }
        return false;
    }

    public function update(User $user, Paciente $paciente): bool
    {
        if ($user->hasRole('administrador')) {
            return true;
        }
        if ($user->hasRole('paciente') && $user->paciente && $user->paciente->id === $paciente->id) {
            return true;
        }
        return false;
    }
}
