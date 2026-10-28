<?php

namespace App\Http\Controllers\Coordinacion;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Horario;
use App\Models\Medico;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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
        ])->validate();

        $activo = $request->boolean('activo');

        if ($activo && $this->tieneSolapamiento(
            $medico->id,
            (int) $validated['dia_semana'],
            $validated['hora_inicio'],
            $validated['hora_fin'],
        )) {
            return back()
                ->withErrors(['horario' => 'Este horario se cruza con otro horario activo del médico en ese día.'])
                ->withInput();
        }

        $horario = $medico->horarios()->create([
            'dia_semana' => $validated['dia_semana'],
            'hora_inicio' => $validated['hora_inicio'],
            'hora_fin' => $validated['hora_fin'],
            'activo' => $activo,
        ]);

        return redirect()
            ->route('coordinacion.medicos.horarios', $medico)
            ->with('success', 'Horario creado correctamente.');
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

        $activo = $request->boolean('activo');

        if ($activo && $this->tieneSolapamiento(
            $medico->id,
            (int) $validated['dia_semana'],
            $validated['hora_inicio'],
            $validated['hora_fin'],
            $horario->id,
        )) {
            return back()
                ->withErrors(['horario' => 'Este horario se cruza con otro horario activo del médico en ese día.'])
                ->withInput();
        }

        $consultasBase = Consulta::where('medico_id', $medico->id)
            ->where('estado', 'confirmada')
            ->whereDate('fecha', '>=', today())
            ->get();
        $horariosAlternativos = $medico->horarios()
            ->where('activo', true)
            ->where('id', '!=', $horario->id)
            ->get();

        $consultasAfectadas = $horario->activo
            ? $consultasBase->filter(function (Consulta $consulta) use ($activo, $validated, $horario, $horariosAlternativos): bool {
                if (! $this->consultaDentroDeHorario($consulta, $horario->dia_semana, $horario->hora_inicio, $horario->hora_fin)) {
                    return false;
                }

                $sigueCubiertaPorHorarioEditado = $activo && $this->consultaDentroDeHorario(
                    $consulta,
                    (int) $validated['dia_semana'],
                    $validated['hora_inicio'],
                    $validated['hora_fin'],
                );

                $sigueCubiertaPorOtroHorario = $horariosAlternativos->contains(
                    fn (Horario $horarioAlternativo): bool => $this->consultaDentroDeHorario(
                        $consulta,
                        $horarioAlternativo->dia_semana,
                        $horarioAlternativo->hora_inicio,
                        $horarioAlternativo->hora_fin,
                    ),
                );

                return ! $sigueCubiertaPorHorarioEditado && ! $sigueCubiertaPorOtroHorario;
            })->count()
            : 0;

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
            'activo' => $activo,
        ]);

        return redirect()
            ->route('coordinacion.medicos.horarios', $medico)
            ->with('success', 'Horario actualizado correctamente.');
    }

    /**
     * Desactivar un horario (soft disable).
     */
    public function destroy(Request $request, Medico $medico, Horario $horario)
    {
        if ($horario->medico_id !== $medico->id) {
            return back()
                ->withErrors(['horario' => 'No tiene permiso para modificar este horario.'])
                ->withInput();
        }

        $horariosAlternativos = $medico->horarios()
            ->where('activo', true)
            ->where('id', '!=', $horario->id)
            ->get();

        $consultasAfectadas = $horario->activo
            ? Consulta::where('medico_id', $medico->id)
                ->where('estado', 'confirmada')
                ->whereDate('fecha', '>=', today())
                ->get()
                ->filter(function (Consulta $consulta) use ($horario, $horariosAlternativos): bool {
                    if (! $this->consultaDentroDeHorario($consulta, $horario->dia_semana, $horario->hora_inicio, $horario->hora_fin)) {
                        return false;
                    }

                    return ! $horariosAlternativos->contains(
                        fn (Horario $horarioAlternativo): bool => $this->consultaDentroDeHorario(
                            $consulta,
                            $horarioAlternativo->dia_semana,
                            $horarioAlternativo->hora_inicio,
                            $horarioAlternativo->hora_fin,
                        ),
                    );
                })
                ->count()
            : 0;

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

    public function eliminar(Medico $medico, Horario $horario): RedirectResponse
    {
        if ($horario->medico_id !== $medico->id) {
            return back()
                ->withErrors(['horario' => 'No tiene permiso para eliminar este horario.']);
        }

        $consultasConfirmadas = $horario->activo
            ? Consulta::query()
                ->where('medico_id', $medico->id)
                ->where('estado', 'confirmada')
                ->whereDate('fecha', '>=', today())
                ->get()
                ->filter(fn (Consulta $consulta): bool => $this->consultaDentroDeHorario(
                    $consulta,
                    $horario->dia_semana,
                    $horario->hora_inicio,
                    $horario->hora_fin,
                ))
            : collect();

        $horariosAlternativos = $medico->horarios()
            ->where('activo', true)
            ->where('id', '!=', $horario->id)
            ->get();

        $consultasSinCobertura = $consultasConfirmadas->filter(
            fn (Consulta $consulta): bool => ! $horariosAlternativos->contains(
                fn (Horario $horarioAlternativo): bool => $this->consultaDentroDeHorario(
                    $consulta,
                    $horarioAlternativo->dia_semana,
                    $horarioAlternativo->hora_inicio,
                    $horarioAlternativo->hora_fin,
                ),
            ),
        );

        if ($consultasSinCobertura->isNotEmpty()) {
            return back()->withErrors([
                'horario' => 'No se puede eliminar este horario porque dejaría citas confirmadas sin cobertura.',
            ]);
        }

        $horario->delete();

        return redirect()
            ->route('coordinacion.medicos.horarios', $medico)
            ->with('success', 'Horario eliminado correctamente.');
    }

    private function tieneSolapamiento(
        int $medicoId,
        int $diaSemana,
        string $horaInicio,
        string $horaFin,
        ?int $horarioId = null,
    ): bool {
        return Horario::query()
            ->where('medico_id', $medicoId)
            ->where('dia_semana', $diaSemana)
            ->where('activo', true)
            ->when($horarioId !== null, fn ($query) => $query->where('id', '!=', $horarioId))
            ->whereTime('hora_inicio', '<', Carbon::parse($horaFin)->format('H:i:s'))
            ->whereTime('hora_fin', '>', Carbon::parse($horaInicio)->format('H:i:s'))
            ->exists();
    }

    private function consultaDentroDeHorario(
        Consulta $consulta,
        int $diaSemana,
        string $horaInicio,
        string $horaFin,
    ): bool {
        $horaConsulta = Carbon::parse($consulta->hora)->format('H:i:s');

        return Carbon::parse($consulta->fecha)->dayOfWeek === $diaSemana
            && $horaConsulta >= Carbon::parse($horaInicio)->format('H:i:s')
            && $horaConsulta < Carbon::parse($horaFin)->format('H:i:s');
    }
}
