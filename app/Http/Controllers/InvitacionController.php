<?php

namespace App\Http\Controllers;

use App\Models\Invitacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InvitacionController extends Controller
{
    /**
     * Show the activation form for a medic invitation.
     */
    public function show(string $token)
    {
        $invitacion = Invitacion::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitacion) {
            return view('auth.activar', [
                'error' => 'La invitación no es válida, ha expirado o ya fue utilizada.',
            ]);
        }

        return view('auth.activar', [
            'token' => $token,
            'email' => $invitacion->email,
            'medicoNombre' => $invitacion->medico->user->name,
            'especialidad' => $invitacion->medico->especialidad?->name ?? 'No asignada',
        ]);
    }

    /**
     * Process the activation: set password and activate the medic account.
     */
    public function activate(Request $request, string $token)
    {
        $invitacion = Invitacion::where('token', $token)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();

        if (! $invitacion) {
            return redirect()->route('medico.activar', ['token' => $token])
                ->withErrors(['token' => 'La invitación no es válida, ha expirado o ya fue utilizada.']);
        }

        $validated = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        // Set password and activate the user
        $user = $invitacion->medico->user;
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        // Activate the medic
        $invitacion->medico->update(['estado' => 'activo']);

        // Mark invitation as used
        $invitacion->update(['used_at' => now()]);

        // Log the medic in automatically
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('medico.dashboard')
            ->with('success', 'Cuenta activada correctamente. Bienvenido, '.$user->name.'.');
    }
}
