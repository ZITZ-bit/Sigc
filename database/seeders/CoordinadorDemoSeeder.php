<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CoordinadorDemoSeeder extends Seeder
{
    public function run(): void
    {
        $rol = Role::where('slug', 'coordinador')->firstOrFail();

        User::create([
            'name' => 'Coordinador Demo',
            'email' => 'coordinador@sigc.test',
            'password' => Hash::make('Password123!'),
            'role_id' => $rol->id,
        ]);
    }
}
