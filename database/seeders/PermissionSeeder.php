<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['nombre' => 'gestionar_usuarios', 'display_name' => 'Gestionar Usuarios'],
            ['nombre' => 'gestionar_clinicas', 'display_name' => 'Gestionar Clínicas'],
            ['nombre' => 'gestionar_medicos', 'display_name' => 'Gestionar Médicos'],
            ['nombre' => 'gestionar_pacientes', 'display_name' => 'Gestionar Pacientes'],
            ['nombre' => 'gestionar_consultas', 'display_name' => 'Gestionar Consultas'],
            ['nombre' => 'gestionar_horarios', 'display_name' => 'Gestionar Horarios'],
            ['nombre' => 'ver_expediente', 'display_name' => 'Ver Expediente'],
            ['nombre' => 'crear_nota_clinica', 'display_name' => 'Crear Nota Clínica'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['nombre' => $permission['nombre']], $permission);
        }

        $admin = Role::where('nombre', 'administrador')->first();
        if ($admin) {
            $admin->permissions()->sync(Permission::all());
        }
    }
}
