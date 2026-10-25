<?php

namespace App\Http\Controllers\Coordinacion;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Especialidad;
use App\Models\Invitacion;
use App\Models\Medico;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class MedicosController extends Controller
{
    /**
     * Show the list of medics.
     */
    public function index(Request $request)
    {
        $query = Medico::with(['user', 'especialidad', 'departamento']);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Specialty filter
        if ($request->filled('especialidad_id')) {
            $query->where('especialidad_id', $request->get('especialidad_id'));
        }

        // Department filter
        if ($request->filled('departamento_id')) {
            $query->where('departamento_id', $request->get('departamento_id'));
        }

        // Status filter
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $medicos = $query->orderBy('created_at', 'desc')->paginate(10);
        $especialidades = Especialidad::orderBy('name')->get();
        $departamentos = Departamento::orderBy('name')->get();

        return view('ui.coordinacion.medicos', [
            'medicos' => $medicos,
            'especialidades' => $especialidades,
            'departamentos' => $departamentos,
            'search' => $request->get('search', ''),
            'filtro_especialidad' => $request->get('especialidad_id', ''),
            'filtro_departamento' => $request->get('departamento_id', ''),
            'filtro_estado' => $request->get('estado', ''),
        ]);
    }

    /**
     * Show the form to register a new medic.
     */
    public function create()
    {
        $especialidades = Especialidad::orderBy('name')->get();
        $departamentos = Departamento::orderBy('name')->get();

        return view('ui.coordinacion.medicos-registrar', [
            'especialidades' => $especialidades,
            'departamentos' => $departamentos,
        ]);
    }

    /**
     * Store a new medic and their user account.
     */
    public function store(Request $request)
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'documento_identidad' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'numero_licencia' => ['nullable', 'string', 'max:100'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'fecha_incorporacion' => ['nullable', 'date'],
            'estado_inicial' => ['required', 'in:pendiente,activo'],
        ])->validate();

        $medicoRole = Role::where('slug', 'medico')->first();
        if (! $medicoRole) {
            return back()->withErrors(['error' => 'El rol médico no existe en el sistema.'])->withInput();
        }

        // Use a transaction to ensure atomicity
        $result = DB::transaction(function () use ($validated, $medicoRole) {
            $fullName = trim($validated['name'].' '.$validated['apellido']);

            $user = User::create([
                'name' => $fullName,
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(16)),
                'role_id' => $medicoRole->id,
            ]);

            $medico = Medico::create([
                'user_id' => $user->id,
                'especialidad_id' => $validated['especialidad_id'] ?? null,
                'departamento_id' => $validated['departamento_id'] ?? null,
                'numero_licencia' => $validated['numero_licencia'] ?? null,
                'telefono' => $validated['telefono'] ?? null,
                'direccion' => $validated['direccion'] ?? null,
                'fecha_incorporacion' => $validated['fecha_incorporacion'] ?? null,
                'estado' => $validated['estado_inicial'],
            ]);

            // Create invitation if estado is pendiente
            if ($validated['estado_inicial'] === 'pendiente') {
                $token = Str::random(60);
                $tokenHash = hash('sha256', $token);

                Invitacion::create([
                    'medico_id' => $medico->id,
                    'email' => $validated['email'],
                    'token' => $token,
                    'token_hash' => $tokenHash,
                    'expires_at' => now()->addDays(7),
                ]);
            }

            return ['user' => $user, 'medico' => $medico];
        });

        return redirect()
            ->route('coordinacion.medicos')
            ->with('success', 'Médico registrado correctamente. Se ha creado su cuenta de usuario.');
    }

    /**
     * Show the form to edit a medic.
     */
    public function edit(Medico $medico)
    {
        $medico->load(['user', 'especialidad', 'departamento']);
        $especialidades = Especialidad::orderBy('name')->get();
        $departamentos = Departamento::orderBy('name')->get();

        return view('ui.coordinacion.medicos-editar', [
            'medico' => $medico,
            'especialidades' => $especialidades,
            'departamentos' => $departamentos,
        ]);
    }

    /**
     * Update a medic's data.
     */
    public function update(Request $request, Medico $medico)
    {
        $validated = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'documento_identidad' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$medico->user_id],
            'telefono' => ['nullable', 'string', 'max:50'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'numero_licencia' => ['nullable', 'string', 'max:100'],
            'especialidad_id' => ['nullable', 'exists:especialidades,id'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'fecha_incorporacion' => ['nullable', 'date'],
            'estado' => ['required', 'in:pendiente,activo,inactivo'],
        ])->validate();

        $medico->user->update([
            'name' => trim($validated['name'].' '.$validated['apellido']),
            'email' => $validated['email'],
        ]);

        $medico->update([
            'especialidad_id' => $validated['especialidad_id'] ?? null,
            'departamento_id' => $validated['departamento_id'] ?? null,
            'numero_licencia' => $validated['numero_licencia'] ?? null,
            'telefono' => $validated['telefono'] ?? null,
            'fecha_incorporacion' => $validated['fecha_incorporacion'] ?? null,
            'estado' => $validated['estado'],
        ]);

        return redirect()
            ->route('coordinacion.medicos')
            ->with('success', 'Datos del médico actualizados correctamente.');
    }

    /**
     * Deactivate a medic account (soft disable).
     */
    public function destroy(Medico $medico)
    {
        $medico->update(['estado' => 'inactivo']);

        return redirect()
            ->route('coordinacion.medicos')
            ->with('success', 'Médico desactivado correctamente.');
    }

    /**
     * Reactivate a medic account.
     */
    public function activate(Medico $medico)
    {
        $medico->update(['estado' => 'activo']);

        return redirect()
            ->route('coordinacion.medicos')
            ->with('success', 'Médico activado correctamente.');
    }

    /**
     * Send/resend invitation to a medic.
     */
    public function sendInvitation(Request $request, Medico $medico)
    {
        // Revoke existing unused invitations
        Invitacion::where('medico_id', $medico->id)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->delete();

        $token = Str::random(60);
        $tokenHash = hash('sha256', $token);

        Invitacion::create([
            'medico_id' => $medico->id,
            'email' => $medico->user->email,
            'token' => $token,
            'token_hash' => $tokenHash,
            'expires_at' => now()->addDays(7),
        ]);

        // Note: In a production environment with SMTP configured,
        // the invitation email would be sent here using Mail facade.
        // Currently MAIL_MAILER=log, so emails are logged to storage/logs/laravel.log

        return redirect()
            ->route('coordinacion.medicos')
            ->with('success', 'Invitación generada para '.$medico->user->email.'. Revise los registros del sistema para el token de activación.');
    }
}
