<?php

namespace Database\Seeders;

use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class QaUsersSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'paciente' => Role::where('slug', 'paciente')->first(),
            'medico' => Role::where('slug', 'medico')->first(),
            'coordinador' => Role::where('slug', 'coordinador')->first(),
        ];

        for ($i = 1; $i <= 5; $i++) {
            $email = sprintf('qa.paciente%02d@sigc.test', $i);
            if (! User::where('email', $email)->exists()) {
                User::create([
                    'name' => 'QA Paciente ' . $i,
                    'email' => $email,
                    'password' => Hash::make('Password123!'),
                    'role_id' => $roles['paciente']->id,
                ]);
            }
        }

        for ($i = 1; $i <= 5; $i++) {
            $email = sprintf('qa.medico%02d@sigc.test', $i);
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name' => 'QA Medico ' . $i,
                    'email' => $email,
                    'password' => Hash::make('Password123!'),
                    'role_id' => $roles['medico']->id,
                ]
            );
            if (! Medico::where('user_id', $user->id)->exists()) {
                Medico::create([
                    'user_id' => $user->id,
                    'estado' => 'activo',
                ]);
            }
        }

        for ($i = 1; $i <= 5; $i++) {
            $email = sprintf('qa.coordinador%02d@sigc.test', $i);
            if (! User::where('email', $email)->exists()) {
                User::create([
                    'name' => 'QA Coordinador ' . $i,
                    'email' => $email,
                    'password' => Hash::make('Password123!'),
                    'role_id' => $roles['coordinador']->id,
                ]);
            }
        }
    }
}
