<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
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

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Muestra el formulario de registro.
     */
    public function showRegister()
    {
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/');
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
}