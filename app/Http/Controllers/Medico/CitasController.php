<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Especialidad;
use App\Notifications\ConsultaEstadoActualizado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CitasController extends Controller
{
    /**
     * Lista las consultas asignadas al médico autenticado.
     */
    public function index(Request $request)
    {
        $medico = Auth::user()->medico;

        if (!$medico) {
            return view('ui.medico.agenda', [
                'consultas' => collect(),
                'medico' => null,
                'filtro_estado' => $request->get('estado', ''),
                'search' => $request->get('search', ''),
            ]);
        }

        $query = Consulta::with(['user', 'especialidad', 'historialClinico'])
            ->where('medico_id', $medico->id)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc');

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
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
        $estados = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        return view('ui.medico.agenda', [
            'consultas' => $consultas,
            'medico' => $medico,
            'estados' => $estados,
            'search' => $request->get('search', ''),
            'filtro_estado' => $request->get('estado', ''),
        ]);
    }

    /**
     * Lista todas las consultas asignadas al médico con filtros detallados.
     */
    public function consultas(Request $request)
    {
        $medico = Auth::user()->medico;

        if (!$medico) {
            return view('ui.medico.consultas', [
                'consultas' => collect(),
                'medico' => null,
                'estados' => ['pendiente', 'confirmada', 'completada', 'cancelada'],
                'filtro_estado' => $request->get('estado', ''),
                'search' => $request->get('search', ''),
            ]);
        }

        $query = Consulta::with(['user', 'especialidad', 'historialClinico'])
            ->where('medico_id', $medico->id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc');

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
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
        $estados = ['pendiente', 'confirmada', 'completada', 'cancelada'];

        return view('ui.medico.consultas', [
            'consultas' => $consultas,
            'medico' => $medico,
            'estados' => $estados,
            'search' => $request->get('search', ''),
            'filtro_estado' => $request->get('estado', ''),
        ]);
    }

    /**
     * Marca una consulta como completada (solo si está confirmada y le pertenece al médico).
     */
    public function marcarCompletada(Request $request, Consulta $consulta)
    {
        $medico = Auth::user()->medico;

        if (!$medico || $consulta->medico_id !== $medico->id) {
            return back()
                ->withErrors(['consulta' => 'No tiene permiso para modificar esta consulta.'])
                ->withInput();
        }

        if ($consulta->estado !== 'confirmada') {
            return back()
                ->withErrors(['estado' => 'Solo se pueden completar consultas con estado "Confirmada".'])
                ->withInput();
        }

        $consulta->update(['estado' => 'completada']);
        $consulta->load('user');

        if ($consulta->user) {
            $consulta->user->notify(new ConsultaEstadoActualizado($consulta, 'confirmada'));
        }

        return redirect()
            ->route('medico.consultas')
            ->with('success', 'Consulta marcada como completada.');
    }

    /**
     * Muestra los horarios de atención del médico autenticado.
     */
    public function horarios()
    {
        $medico = Auth::user()->medico;

        if (!$medico) {
            return view('ui.medico.horario', [
                'medico' => null,
                'horarios' => collect(),
                'diasSemana' => [
                    0 => 'Domingo',
                    1 => 'Lunes',
                    2 => 'Martes',
                    3 => 'Miércoles',
                    4 => 'Jueves',
                    5 => 'Viernes',
                    6 => 'Sábado',
                ],
            ]);
        }

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

        return view('ui.medico.horario', [
            'medico' => $medico,
            'horarios' => $horarios,
            'diasSemana' => $diasSemana,
        ]);
    }
}
