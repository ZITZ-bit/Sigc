<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invite;
use App\Models\Paciente;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PacienteAdminController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.panel', ['view' => 'pacientes']);
    }

    public function invite(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);
        $role = Role::where('nombre', 'paciente')->firstOrFail();
        $invite = Invite::create([
            'email' => $validated['email'],
            'token' => Str::random(64),
            'role_id' => $role->id,
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('admin.panel', ['view' => 'pacientes'])
            ->with('success', 'Invitación de paciente creada. Comparte el enlace para activar su cuenta.')
            ->with('invite_url', route('invite.accept', $invite->token));
    }

    public function updateStatus(Request $request, Paciente $paciente)
    {
        $validated = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $paciente->user()->update(['activo' => $validated['activo']]);

        return redirect()->route('admin.panel', ['view' => 'pacientes'])
            ->with('success', 'Estado del paciente actualizado.');
    }

    public function update(Request $request, Paciente $paciente)
    {
        $user = $paciente->user;
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'telefono' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('admin.panel', ['view' => 'pacientes'])
            ->with('success', 'Datos del paciente actualizados.');
    }
}
