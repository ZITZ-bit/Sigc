<?php

namespace App\Http\Controllers;

use App\Models\Invitacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class InvitacionController extends Controller
{
    /**
     * Show the activation form for a medic invitation.
     */
    public function show(string $token)
    {
        $invitacion = $this->findValidInvitation($token);

        if (! $invitacion) {
            return view('auth.activar', [
                'error' => 'La invitación no es válida, ha expirado o ya fue utilizada.',
            ]);
        }

        return view('auth.activar', [
            'token' => $token,
            'email' => $invitacion->email,
            'medicoNombre' => $invitacion->medico?->user?->name ?? 'Médico',
            'especialidad' => $invitacion->medico?->especialidad?->name ?? 'No asignada',
        ]);
    }

    /**
     * Process the activation: set password and activate the medic account.
     */
    public function activate(Request $request, string $token)
    {
        $validated = Validator::make($request->all(), [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ])->validate();

        $tokenHash = $this->hashToken($token);

        $user = DB::transaction(function () use ($tokenHash, $validated): ?User {
            $invitacion = Invitacion::where('token_hash', $tokenHash)
                ->whereNull('used_at')
                ->where('expires_at', '>', now())
                ->lockForUpdate()
                ->first();

            if (! $invitacion) {
                return null;
            }

            $medico = $invitacion->medico;

            if (! $medico || ! $medico->user) {
                return null;
            }

            $medico->user->update([
                'password' => Hash::make($validated['password']),
            ]);

            $medico->update(['estado' => 'activo']);

            $invitacion->update(['used_at' => now()]);

            return $medico->user;
        });

        if (! $user) {
            return redirect()->route('medico.activar', ['token' => $token])
                ->withErrors(['token' => 'La invitación no es válida, ha expirado o ya fue utilizada.']);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('medico.dashboard')
            ->with('success', 'Cuenta activada correctamente. Bienvenido, '.$user->name.'.');
    }

    /**
     * Busca una invitación válida usando el hash del token recibido.
     */
    private function findValidInvitation(string $token): ?Invitacion
    {
        return Invitacion::with(['medico.user', 'medico.especialidad'])
            ->where('token_hash', $this->hashToken($token))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->first();
    }

    /**
     * Calcula el hash almacenado del token de invitación.
     */
    private function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
