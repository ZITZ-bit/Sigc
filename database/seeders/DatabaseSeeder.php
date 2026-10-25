<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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

        // Crear usuarios de prueba
        User::create([
            'name' => 'Paciente Demo',
            'email' => 'paciente@sigc.test',
            'password' => Hash::make('Paciente123!'),
            'role_id' => $paciente->id,
        ]);

        User::create([
            'name' => 'Medico Demo',
            'email' => 'medico@sigc.test',
            'password' => Hash::make('Medico123!'),
            'role_id' => $medico->id,
        ]);

        User::create([
            'name' => 'Coordinador Demo',
            'email' => 'coordinador@sigc.test',
            'password' => Hash::make('Coordinador123!'),
            'role_id' => $coordinador->id,
        ]);
    }
}