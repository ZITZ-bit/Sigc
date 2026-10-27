<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route($this->dashboardRouteForUser(Auth::user()));
        }

        return view('login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function login(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'email' => ['required', 'email'],
            'password' => ['required'],
        ])->validate();

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['password' => 'Credenciales incorrectas. Inténtalo de nuevo.']);
        }

        if (! $this->hasActiveMedicoAccount(Auth::user())) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'La cuenta médica no está activa. Contacte con coordinación para activarla.']);
        }

        $request->session()->regenerate();

        return redirect()->route($this->dashboardRouteForUser(Auth::user()));
    }

    /**
     * Muestra el formulario de registro.
     */
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route($this->dashboardRouteForUser(Auth::user()));
        }

        return view('register');
    }

    /**
     * Procesa el registro de un nuevo usuario.
     */
    public function register(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $pacienteRole = Role::where('slug', 'paciente')->first();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $pacienteRole?->id,
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('paciente.dashboard');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Determina si el usuario puede acceder según el estado de su ficha médica.
     */
    private function hasActiveMedicoAccount(User $user): bool
    {
        if ($user->role?->slug !== 'medico') {
            return true;
        }

        return $user->medico?->estado === 'activo';
    }

    /**
     * Obtiene el nombre de la ruta del dashboard según el rol del usuario.
     */
    private function dashboardRouteForUser(User $user): string
    {
        return match ($user->role?->slug) {
            'medico' => 'medico.dashboard',
            'coordinador' => 'coordinacion.dashboard',
            default => 'paciente.dashboard',
        };
    }
}
