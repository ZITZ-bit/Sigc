<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Especialidad;
use App\Models\Invite;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MedicoAdminController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.panel', ['view' => 'equipo']);
    }

    public function inviteMedico(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'clinica_id' => ['required', 'exists:clinicas,id'],
            'especialidad_id' => ['required', 'exists:especialidades,id'],
        ]);

        $roleMedico = Role::where('nombre', 'medico')->firstOrFail();

        $invite = Invite::create([
            'email' => $validated['email'],
            'token' => Str::random(64),
            'role_id' => $roleMedico->id,
            'clinica_id' => $validated['clinica_id'],
            'especialidad_id' => $validated['especialidad_id'],
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('admin.panel', ['view' => 'equipo'])
            ->with('success', 'Invitación creada para el médico. Comparte el enlace para que active su cuenta.')
            ->with('invite_url', route('invite.accept', $invite->token));
    }

    public function inviteAdmin(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $roleAdmin = Role::where('nombre', 'administrador')->firstOrFail();

        $invite = Invite::create([
            'email' => $validated['email'],
            'token' => Str::random(64),
            'role_id' => $roleAdmin->id,
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->route('admin.panel', ['view' => 'equipo'])
            ->with('success', 'Invitación creada para el administrador. Comparte el enlace para que active su cuenta.')
            ->with('invite_url', route('invite.accept', $invite->token));
    }
}
