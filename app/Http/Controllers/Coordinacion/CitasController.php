<?php

namespace App\Http\Controllers\Coordinacion;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Horario;
use App\Models\Medico;
use App\Notifications\ConsultaAsignada;
use App\Notifications\ConsultaEstadoActualizado;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CitasController extends Controller
{
    /**
     * Listar consultas con filtros y paginación.
     */
    public function index(Request $request)
    {
        $query = Consulta::with(['user', 'medico.user', 'especialidad', 'historialClinico'])
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc');

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        // Filtro por fecha
        if ($request->filled('fecha')) {
            $query->where('fecha', $request->get('fecha'));
        }

        // Búsqueda por nombre de paciente
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $consultas = $query->paginate(10);
        $especialidades = Especialidad::orderBy('name')->get();
        $medicos = Medico::with('user')->where('estado', 'activo')->orderBy('id')->get();
        $estados = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        return view('ui.coordinacion.citas', [
            'consultas' => $consultas,
            'especialidades' => $especialidades,
            'medicos' => $medicos,
            'estados' => $estados,
            'search' => $request->get('search', ''),
            'filtro_estado' => $request->get('estado', ''),
            'filtro_fecha' => $request->get('fecha', ''),
        ]);
    }

    /**
     * Asignar o cambiar el médico de una consulta.
     */
    public function asignarMedico(Request $request, Consulta $consulta)
    {
        if (! in_array($consulta->estado, ['pendiente', 'confirmada'], true)) {
            return back()
                ->withErrors(['medico_id' => 'Solo se puede asignar un médico a consultas pendientes o confirmadas.'])
                ->withInput();
        }

        $validated = Validator::make($request->all(), [
            'medico_id' => ['required', 'exists:medicos,id'],
        ])->validate();

        $medico = Medico::with('user')->find($validated['medico_id']);

        // Verificar que el médico esté activo
        if ($medico->estado !== 'activo') {
            return back()
                ->withErrors(['medico_id' => 'El médico seleccionado no está activo.'])
                ->withInput();
        }

        // Verificar conflicto de fecha/hora con otras consultas activas del mismo médico
        // (excluyendo la consulta actual)
        $conflicto = Consulta::where('medico_id', $medico->id)
            ->where('fecha', $consulta->fecha)
            ->where('hora', $consulta->hora)
            ->where('id', '!=', $consulta->id)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->exists();

        if ($conflicto) {
            return back()
                ->withErrors(['medico_id' => 'El médico ya tiene una consulta activa en esa fecha y hora.'])
                ->withInput();
        }

        if (! $this->tieneHorarioActivo($medico->id, $consulta)) {
            return back()
                ->withErrors(['medico_id' => $this->mensajeHorarioNoDisponible($consulta)])
                ->withInput();
        }

        $consulta->update([
            'medico_id' => $medico->id,
            'estado' => 'confirmada',
        ]);
        $consulta->load(['user', 'especialidad']);
        $consulta->load('medico.user');

        if ($consulta->user) {
            $consulta->user->notify(new ConsultaEstadoActualizado($consulta));
        }

        // Notificar al médico asignado
        if ($medico->user) {
            $medico->user->notify(new ConsultaAsignada($consulta));
        }

        return redirect()
            ->route('coordinacion.citas')
            ->with('success', 'Médico asignado correctamente a la consulta.');
    }

    /**
     * Cambiar el estado de una consulta.
     */
    public function cambiarEstado(Request $request, Consulta $consulta)
    {
        $validated = Validator::make($request->all(), [
            'estado' => ['required', 'in:pendiente,confirmada,completada,cancelada'],
        ])->validate();

        $estadoActual = $consulta->estado;
        $nuevoEstado = $validated['estado'];

        // Definir transiciones permitidas
        $transicionesPermitidas = [
            'pendiente' => ['confirmada', 'cancelada'],
            'confirmada' => ['completada', 'cancelada'],
            'completada' => [],
            'cancelada' => [],
        ];

        if (! in_array($nuevoEstado, $transicionesPermitidas[$estadoActual] ?? [], true)) {
            return back()
                ->withErrors(['estado' => 'No se puede cambiar de "'.$estadoActual.'" a "'.$nuevoEstado.'".'])
                ->withInput();
        }

        // Si se confirma, requiere médico asignado y disponibilidad en ese horario
        if ($nuevoEstado === 'confirmada') {
            if (! $consulta->medico_id) {
                return back()
                    ->withErrors(['estado' => 'No se puede confirmar una consulta sin médico asignado.'])
                    ->withInput();
            }

            if (! $this->tieneHorarioActivo((int) $consulta->medico_id, $consulta)) {
                return back()
                    ->withErrors(['estado' => $this->mensajeHorarioNoDisponible($consulta)])
                    ->withInput();
            }

            // Verificar que no haya otra consulta confirmada del mismo médico en esa fecha/hora
            $conflictoConfirmada = Consulta::where('medico_id', $consulta->medico_id)
                ->where('fecha', $consulta->fecha)
                ->where('hora', $consulta->hora)
                ->where('id', '!=', $consulta->id)
                ->where('estado', 'confirmada')
                ->exists();

            if ($conflictoConfirmada) {
                return back()
                    ->withErrors(['estado' => 'El médico ya tiene una consulta confirmada en esa fecha y hora.'])
                    ->withInput();
            }
        }

        $consulta->update(['estado' => $nuevoEstado]);
        $consulta->load(['user', 'medico.user', 'especialidad']);

        if ($nuevoEstado === 'confirmada' && $consulta->user) {
            $consulta->user->notify(new ConsultaEstadoActualizado($consulta));
        }

        $estadoLabel = match ($nuevoEstado) {
            'pendiente' => 'Pendiente',
            'confirmada' => 'Confirmada',
            'completada' => 'Completada',
            'cancelada' => 'Cancelada',
        };

        return redirect()
            ->route('coordinacion.citas')
            ->with('success', 'Estado de la consulta actualizado a "'.$estadoLabel.'".');
    }

    public function destroy(Consulta $consulta): RedirectResponse
    {
        if ($consulta->historialClinico()->exists()) {
            return back()->withErrors([
                'consulta' => 'No se puede eliminar esta cita porque tiene un historial clínico asociado.',
            ]);
        }

        $consulta->delete();

        return redirect()
            ->route('coordinacion.citas')
            ->with('success', 'La cita se eliminó correctamente.');
    }

    private function tieneHorarioActivo(int $medicoId, Consulta $consulta): bool
    {
        $horaConsulta = Carbon::parse($consulta->hora)->format('H:i:s');

        return Horario::query()
            ->where('medico_id', $medicoId)
            ->where('dia_semana', Carbon::parse($consulta->fecha)->dayOfWeek)
            ->where('activo', true)
            ->whereTime('hora_inicio', '<=', $horaConsulta)
            ->whereTime('hora_fin', '>', $horaConsulta)
            ->exists();
    }

    private function mensajeHorarioNoDisponible(Consulta $consulta): string
    {
        $diasSemana = ['domingo', 'lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado'];
        $diaSemana = $diasSemana[Carbon::parse($consulta->fecha)->dayOfWeek];
        $hora = Carbon::parse($consulta->hora)->format('H:i');

        return "El médico no tiene un horario activo que cubra el {$diaSemana} a las {$hora}. Verifica que ese día esté activo y que la hora esté dentro del rango; la hora final no se incluye.";
    }
}
