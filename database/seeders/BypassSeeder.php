<?php

namespace Database\Seeders;

use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BypassSeeder extends Seeder
{
    /**
     * Bloque de usuarios de prueba PREVIOS — DESACTIVADO.
     *
     * Estos tres usuarios (Paciente, Medico, Coordinador) ya no se crean al
     * ejecutar este seeder. El bloque se conserva comentado únicamente con
     * fines documentales y de trazabilidad. No se reutiliza ni se actualiza.
     *
     * Usuario 1: Paciente, paciente@sigc.test, Paciente123!
     * Usuario 2: Medico, medico@sigc.test, Medico123!
     * Usuario 3: Coordinador, coordinador@sigc.test, Password123!
     */
    /*
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
    */

    public function run(): void
    {
        // Tres usuarios de prueba activos, uno por cada rol.
        // Contraseña de todos: password123 (almacenada con Hash::make).
        // Usuarios exclusivamente de prueba; no usar en producción.
        $usuarios = [
            [
                'name' => 'Valentina Ríos',
                'email' => 'valentina.rios@sigc.test',
                'password' => 'password123',
                'role' => 'paciente',
                'create_medico_profile' => false,
            ],
            [
                'name' => 'Gabriel Mendoza',
                'email' => 'gabriel.mendoza@sigc.test',
                'password' => 'password123',
                'role' => 'medico',
                'create_medico_profile' => true,
            ],
            [
                'name' => 'Laura Fernández',
                'email' => 'laura.fernandez@sigc.test',
                'password' => 'password123',
                'role' => 'coordinador',
                'create_medico_profile' => false,
            ],
        ];

        foreach ($usuarios as $usuario) {
            $rol = Role::where('slug', $usuario['role'])->firstOrFail();

            $user = User::updateOrCreate(
                ['email' => $usuario['email']],
                [
                    'name' => $usuario['name'],
                    'password' => Hash::make($usuario['password']),
                    'role_id' => $rol->id,
                ]
            );

            // El panel del médico requiere un perfil en la tabla `medicos`
            // con estado 'activo' (middleware Authenticate y dashboard médico
            // consultan $user->medico). Se crea respetando la relación 1:1.
            if ($usuario['create_medico_profile'] === true) {
                Medico::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'estado' => 'activo',
                    ]
                );
            }
        }
    }
}
