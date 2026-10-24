<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('nombre', 'administrador')->first();

        $user = User::firstOrCreate(
            ['email' => 'admin@sigc.mx'],
            [
                'name' => 'María',
                'apellido' => 'Cruz',
                'username' => 'mariacruz',
                'password' => Hash::make('admin123456'),
                'activo' => true,
            ]
        );

        if ($adminRole && !$user->hasRole('administrador')) {
            $user->assignRole($adminRole);
        }
    }
}
