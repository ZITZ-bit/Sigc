<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['nombre' => 'paciente', 'display_name' => 'Paciente', 'descripcion' => 'Usuario paciente'],
            ['nombre' => 'medico', 'display_name' => 'Médico', 'descripcion' => 'Usuario médico'],
            ['nombre' => 'administrador', 'display_name' => 'Administrador', 'descripcion' => 'Usuario administrador'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['nombre' => $role['nombre']], $role);
        }
    }
}
