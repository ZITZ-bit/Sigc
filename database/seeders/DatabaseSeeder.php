<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles
        $paciente = Role::create([
            'name' => 'Paciente',
            'slug' => 'paciente',
        ]);

        $medico = Role::create([
            'name' => 'Médico',
            'slug' => 'medico',
        ]);

        $coordinador = Role::create([
            'name' => 'Coordinador',
            'slug' => 'coordinador',
        ]);
    }
}
