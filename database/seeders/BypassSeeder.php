<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BypassSeeder extends Seeder
{
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Paciente',
                'email' => 'paciente@sigc.test',
                'password' => 'Paciente123!',
                'role' => 'paciente',
            ],
            [
                'name' => 'Medico',
                'email' => 'medico@sigc.test',
                'password' => 'Medico123!',
                'role' => 'medico',
            ],
            [
                'name' => 'Coordinador',
                'email' => 'coordinador@sigc.test',
                'password' => 'Password123!',
                'role' => 'coordinador',
            ],
        ];

        foreach ($usuarios as $usuario) {
            $rol = Role::where('slug', $usuario['role'])->firstOrFail();

            User::updateOrCreate(
                ['email' => $usuario['email']],
                [
                    'name' => $usuario['name'],
                    'password' => Hash::make($usuario['password']),
                    'role_id' => $rol->id,
                ]
            );
        }
    }
}
