<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->intended(Auth::user()->redirectToPanel());
        }
        return view('register');
    }

    public function register(Request $request)
    {
        // Solo pacientes pueden registrarse públicamente
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'clinic' => ['nullable', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($validated) {
            $user = User::create([
                'name' => $validated['name'],
                'apellido' => $validated['last_name'],
                'username' => $validated['username'] ?? null,
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'activo' => true,
            ]);

            // Asignar SIEMPRE rol paciente - nunca desde input
            $rolePaciente = Role::firstOrCreate(
                ['nombre' => 'paciente'],
                ['display_name' => 'Paciente', 'descripcion' => 'Paciente registrado públicamente']
            );
            $user->assignRole($rolePaciente);

            // Crear registro de paciente
            Paciente::create([
                'user_id' => $user->id,
                'expediente_numero' => 'EXP-' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
            ]);

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('paciente.panel')->with('success', 'Cuenta de paciente creada exitosamente.');
    }
}
