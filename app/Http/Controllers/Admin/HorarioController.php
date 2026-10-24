<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'medico_id' => ['required', 'exists:medicos,id'],
            'dia_semana' => ['required', 'in:Lunes,Martes,Miércoles,Jueves,Viernes,Sábado,Domingo'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'duracion_minutos' => ['nullable', 'integer', 'min:15', 'max:120'],
        ]);

        Horario::create([
            'medico_id' => $validated['medico_id'],
            'dia_semana' => $validated['dia_semana'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'duracion_minutos' => $validated['duracion_minutos'] ?? 30,
            'activo' => true,
        ]);

        return redirect()->route('admin.panel', ['view' => 'consultas', 'tab' => 'hours'])
            ->with('success', 'Horario creado correctamente.');
    }

    public function updateStatus(Request $request, Horario $horario)
    {
        $validated = $request->validate([
            'activo' => ['required', 'boolean'],
        ]);

        $horario->update(['activo' => $validated['activo']]);

        return redirect()->route('admin.panel', ['view' => 'consultas', 'tab' => 'hours'])
            ->with('success', 'Disponibilidad actualizada.');
    }
}
