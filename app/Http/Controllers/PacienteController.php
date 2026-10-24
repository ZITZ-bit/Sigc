<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Horario;
use App\Models\Medico;
use App\Models\Notificacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PacienteController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $paciente = $user->paciente()->with('consultas.medico.user', 'consultas.medico.especialidad')->first();
        $medicos = Medico::with('user', 'especialidad')->get();
        $horarios = Horario::with('medico.user', 'medico.especialidad')->where('activo', true)->get();
        $notificaciones = Notificacion::where('user_id', $user->id)->orderBy('created_at', 'desc')->get();

        return view('ui.pacientes.paciente-portal', compact('user', 'paciente', 'medicos', 'horarios', 'notificaciones'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'telefono' => ['nullable', 'string', 'max:20'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'tipo_sangre' => ['nullable', 'in:O+,A+,B+,AB+,O-,A-,B-,AB-'],
        ]);

        $user->update($validated);

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function solicitarConsulta(Request $request)
    {
        $user = Auth::user();
        $paciente = $user->paciente;

        $validated = $request->validate([
            'medico_id' => ['required', 'exists:medicos,id'],
            'fecha_hora' => ['required', 'date'],
            'motivo' => ['nullable', 'string', 'max:500'],
        ]);

        $medico = Medico::findOrFail($validated['medico_id']);

        Consulta::create([
            'paciente_id' => $paciente->id,
            'medico_id' => $medico->id,
            'clinica_id' => $medico->clinica_id,
            'fecha_hora' => $validated['fecha_hora'],
            'motivo' => $validated['motivo'],
            'estado' => 'pendiente',
        ]);

        return redirect()->back()->with('success', 'Solicitud de consulta enviada. Pendiente de confirmación.');
    }
}
