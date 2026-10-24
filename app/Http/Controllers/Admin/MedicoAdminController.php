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
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrador']);
    }

    public function index()
    {
        $medicos = \App\Models\Medico::with('user', 'especialidad', 'clinica')->get();
        $clinicas = Clinica::all();
        $especialidades = Especialidad::all();
        return view('ui.administracion.admin-portal', compact('medicos', 'clinicas', 'especialidades'));
    }

    public function inviteMedico(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'clinica_id' => ['required', 'exists:clinicas,id'],
            'especialidad_id' => ['required', 'exists:especialidades,id'],
        ]);

        $roleMedico = Role::where('nombre', 'medico')->firstOrFail();

        Invite::create([
            'email' => $validated['email'],
            'token' => Str::random(64),
            'role_id' => $roleMedico->id,
            'clinica_id' => $validated['clinica_id'],
            'especialidad_id' => $validated['especialidad_id'],
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->back()->with('success', 'Invitación enviada al médico. Enlace generado (revisar logs/enlace de invitación).');
    }

    public function inviteAdmin(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
        ]);

        $roleAdmin = Role::where('nombre', 'administrador')->firstOrFail();

        Invite::create([
            'email' => $validated['email'],
            'token' => Str::random(64),
            'role_id' => $roleAdmin->id,
            'invited_by' => Auth::id(),
            'expires_at' => now()->addDays(7),
        ]);

        return redirect()->back()->with('success', 'Invitación enviada al administrador.');
    }
}
