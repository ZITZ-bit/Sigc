<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConsultaController extends Controller
{
    public function index()
    {
        return redirect()->route('admin.panel', ['view' => 'consultas']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'medico_id' => ['required', 'exists:medicos,id'],
            'fecha_hora' => ['required', 'date'],
            'motivo' => ['nullable', 'string', 'max:500'],
            'estado' => ['nullable', 'in:pendiente,confirmada,completada,cancelada'],
        ]);

        $medico = Medico::with('horarios')->findOrFail($validated['medico_id']);
        $inicio = Carbon::parse($validated['fecha_hora']);
        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $horario = $medico->horarios
            ->where('activo', true)
            ->first(fn ($horario) => $horario->dia_semana === $diasSemana[$inicio->dayOfWeek]
                && $horario->hora_inicio <= $inicio->format('H:i:s')
                && $horario->hora_fin > $inicio->format('H:i:s'));

        if (!$horario) {
            throw ValidationException::withMessages([
                'fecha_hora' => 'El médico no tiene disponibilidad activa a esa hora.',
            ]);
        }

        $duracion = $horario->duracion_minutos ?: 30;
        $fin = $inicio->copy()->addMinutes($duracion);
        $finHorario = $inicio->copy()->setTimeFromTimeString($horario->hora_fin);

        if ($fin->greaterThan($finHorario)) {
            throw ValidationException::withMessages([
                'fecha_hora' => 'La consulta termina fuera del horario de atención del médico.',
            ]);
        }

        $haySolapamiento = $medico->consultas()
            ->whereDate('fecha_hora', $inicio->toDateString())
            ->where('estado', '!=', 'cancelada')
            ->get(['fecha_hora', 'duracion_minutos'])
            ->contains(function (Consulta $consulta) use ($inicio, $fin) {
                $consultaInicio = $consulta->fecha_hora;
                $consultaFin = $consultaInicio->copy()->addMinutes($consulta->duracion_minutos);

                return $inicio->lessThan($consultaFin) && $fin->greaterThan($consultaInicio);
            });

        if ($haySolapamiento) {
            throw ValidationException::withMessages([
                'fecha_hora' => 'El médico ya tiene una consulta en ese horario.',
            ]);
        }

        Consulta::create([
            'paciente_id' => $validated['paciente_id'],
            'medico_id' => $validated['medico_id'],
            'clinica_id' => $medico->clinica_id,
            'fecha_hora' => $inicio,
            'duracion_minutos' => $duracion,
            'motivo' => $validated['motivo'] ?? null,
            'estado' => $validated['estado'] ?? 'confirmada',
        ]);

        return redirect()->route('admin.panel', ['view' => 'consultas'])
            ->with('success', 'Consulta creada correctamente.');
    }

    public function update(Request $request, Consulta $consulta)
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:pendiente,confirmada,completada,cancelada'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $consulta->update($validated);
        return redirect()->route('admin.panel', ['view' => 'consultas'])
            ->with('success', 'Consulta actualizada.');
    }
}
