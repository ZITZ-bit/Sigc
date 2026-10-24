<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class InviteController extends Controller
{
    public function showAcceptForm(string $token)
    {
        $invite = Invite::where('token', $token)->firstOrFail();

        if (!$invite->isValid()) {
            abort(403, 'Esta invitación no es válida o ya ha sido aceptada.');
        }

        return view('auth.accept-invite', compact('invite'));
    }

    public function accept(Request $request, string $token)
    {
        $invite = Invite::where('token', $token)->firstOrFail();

        if (!$invite->isValid()) {
            abort(403, 'Esta invitación no es válida o ya ha sido aceptada.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'cedula_profesional' => ['nullable', 'string', 'max:255'],
            'anios_experiencia' => ['nullable', 'integer', 'min:0'],
            'consultorio' => ['nullable', 'string', 'max:255'],
        ]);

        $user = DB::transaction(function () use ($validated, $invite) {
            $user = User::create([
                'name' => $validated['name'],
                'apellido' => $validated['apellido'],
                'username' => $validated['username'] ?? null,
                'email' => $invite->email,
                'password' => Hash::make($validated['password']),
                'activo' => true,
            ]);

            $user->assignRole($invite->role);

            if ($invite->role->nombre === 'medico') {
                Medico::create([
                    'user_id' => $user->id,
                    'clinica_id' => $invite->clinica_id,
                    'especialidad_id' => $invite->especialidad_id,
                    'cedula_profesional' => $validated['cedula_profesional'] ?? null,
                    'anios_experiencia' => $validated['anios_experiencia'] ?? null,
                    'consultorio' => $validated['consultorio'] ?? null,
                ]);
            }

            $invite->accepted_at = now();
            $invite->save();

            return $user;
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect($user->redirectToPanel())->with('success', 'Cuenta activada exitosamente.');
    }
}
