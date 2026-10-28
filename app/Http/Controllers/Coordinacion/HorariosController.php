<?php

namespace App\Http\Controllers\Coordinacion;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Horario;
use App\Models\Medico;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HorariosController extends Controller
{
    /**
     * Listar horarios de un médico.
     */
    public function index(Medico $medico)
    {
        $horarios = $medico->horarios()
            ->orderBy('dia_semana', 'asc')
            ->orderBy('hora_inicio', 'asc')
            ->get();

        $diasSemana = [
            0 => 'Domingo',
            1 => 'Lunes',
            2 => 'Martes',
            3 => 'Miércoles',
            4 => 'Jueves',
            5 => 'Viernes',
            6 => 'Sábado',
        ];

        return view('ui.coordinacion.horarios', [
            'medico' => $medico,
            'horarios' => $horarios,
            'diasSemana' => $diasSemana,
        ]);
    }

    /**
     * Crear un nuevo horario para el médico.
     */
    public function store(Request $request, Medico $medico)
    {
        $validated = Validator::make($request->all(), [
            'dia_semana' => ['required', 'integer', 'min:0', 'max:6'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'activo' => ['sometimes', 'boolean'],
            'return_to' => ['sometimes', 'in:edit'],
        ])->validate();

        // Verificar que no exista solapamiento con otro horario del mismo médico y día
        $solapamiento = Horario::where('medico_id', $medico->id)
            ->where('dia_semana', $validated['dia_semana'])
            ->where(function ($q) use ($validated) {
                $q->where(function ($inner) use ($validated) {
                    $inner->where('hora_inicio', '<', $validated['hora_fin'])
                        ->where('hora_fin', '>', $validated['hora_inicio']);
                });
            })
            ->exists();

        if ($solapamiento) {
            return back()
                ->withErrors(['horario' => 'Ya existe un horario que se solapa con este rango para este médico y día.'])
                ->withInput();
        }

        // Verificar si existen consultas futuras asignadas a este médico que quedarían fuera del nuevo horario
        // (solo cuando se desactiva o modifica un horario existente - no aplica en creación)
        // La validación de consultas afectadas se maneja al editar/eliminar.

        $horario = $medico->horarios()->create([
            'dia_semana' => $validated['dia_semana'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'activo' => $request->boolean('activo'),
        ]);

        $redirect = isset($validated['return_to'])
            ? redirect()->route('coordinacion.medicos.editar', $medico)
            : redirect()->route('coordinacion.medicos.horarios', $medico);

        return $redirect->with('success', 'Horario creado correctamente.');
    }

    /**
     * Actualizar un horario existente.
     */
    public function update(Request $request, Medico $medico, Horario $horario)
    {
        // Verificar que el horario pertenezca al médico
        if ($horario->medico_id !== $medico->id) {
            return back()
                ->withErrors(['horario' => 'No tiene permiso para modificar este horario.'])
                ->withInput();
        }

        $validated = Validator::make($request->all(), [
            'dia_semana' => ['required', 'integer', 'min:0', 'max:6'],
            'hora_inicio' => ['required', 'date_format:H:i'],
            'hora_fin' => ['required', 'date_format:H:i', 'after:hora_inicio'],
            'activo' => ['sometimes', 'boolean'],
        ])->validate();

        // Verificar solapamiento con otros horarios del mismo médico y día (excluyendo este)
        $solapamiento = Horario::where('medico_id', $medico->id)
            ->where('dia_semana', $validated['dia_semana'])
            ->where('id', '!=', $horario->id)
            ->where(function ($q) use ($validated) {
                $q->where(function ($inner) use ($validated) {
                    $inner->where('hora_inicio', '<', $validated['hora_fin'])
                        ->where('hora_fin', '>', $validated['hora_inicio']);
                });
            })
            ->exists();

        if ($solapamiento) {
            return back()
                ->withErrors(['horario' => 'Ya existe un horario que se solapa con este rango para este médico y día.'])
                ->withInput();
        }

        // Verificar consultas futuras que quedarían fuera del nuevo horario si se reduce o desactiva
        $consultasAfectadas = 0;

        $consultasBase = Consulta::where('medico_id', $medico->id)
            ->where('estado', 'confirmada')
            ->whereDate('fecha', '>=', today())
            ->get();

        $consultasEnRango = $consultasBase->filter(function ($c) use ($horario) {
            return Carbon::parse($c->fecha)->dayOfWeek === $horario->dia_semana
                && $c->hora >= $horario->hora_inicio
                && $c->hora < $horario->hora_fin;
        });

        if (! $request->boolean('activo')) {
            // Al desactivar, todas las consultas confirmadas dentro del rango actual quedan sin cobertura.
            $consultasAfectadas = $consultasEnRango->count();
        } else {
            // Al cambiar día u horas, solo peligran las consultas del rango anterior que queden fuera del nuevo.
            $consultasQuery = $consultasEnRango;

            if ((int) $validated['dia_semana'] === (int) $horario->dia_semana) {
                $consultasQuery = $consultasQuery->filter(function ($c) use ($validated) {
                    return $c->hora < $validated['hora_inicio'] || $c->hora >= $validated['hora_fin'];
                });
            }

            $consultasAfectadas = $consultasQuery->count();
        }

        if ($consultasAfectadas > 0) {
            return back()
                ->withErrors([
                    'horario' => "No se puede modificar o desactivar este horario porque hay {$consultasAfectadas} consulta(s) confirmada(s) que quedarían sin cobertura.",
                ])
                ->withInput();
        }

        $horario->update([
            'dia_semana' => $validated['dia_semana'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'activo' => $request->boolean('activo'),
        ]);

        return redirect()
            ->route('coordinacion.medicos.horarios', $medico)
            ->with('success', 'Horario actualizado correctamente.');
    }

    /**
     * Desactivar un horario (soft disable).
     */
    public function destroy(Medico $medico, Horario $horario)
    {
        if ($horario->medico_id !== $medico->id) {
            return back()
                ->withErrors(['horario' => 'No tiene permiso para modificar este horario.'])
                ->withInput();
        }

        // Verificar consultas futuras confirmadas que quedarían sin horario
        $consultasAfectadas = Consulta::where('medico_id', $medico->id)
            ->where('estado', 'confirmada')
            ->whereDate('fecha', '>=', today())
            ->get()
            ->filter(function ($c) use ($horario) {
                return Carbon::parse($c->fecha)->dayOfWeek === $horario->dia_semana
                    && $c->hora >= $horario->hora_inicio
                    && $c->hora < $horario->hora_fin;
            })->count();

        if ($consultasAfectadas > 0) {
            return back()
                ->withErrors([
                    'horario' => "No se puede desactivar este horario porque hay {$consultasAfectadas} consulta(s) confirmada(s) en ese rango horario.",
                ])
                ->withInput();
        }

        $horario->update(['activo' => false]);

        return redirect()
            ->route('coordinacion.medicos.horarios', $medico)
            ->with('success', 'Horario desactivado correctamente.');
    }
}
