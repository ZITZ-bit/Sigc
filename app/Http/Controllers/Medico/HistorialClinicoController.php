<?php

namespace App\Http\Controllers\Medico;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\HistorialClinico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HistorialClinicoController extends Controller
{
    public function index(Request $request)
    {
        $medico = Auth::user()->medico;

        if (! $medico) {
            return view('ui.medico.historial_clinico', [
                'registros' => collect(),
                'medico' => null,
            ]);
        }

        $registros = HistorialClinico::with(['consulta.especialidad', 'paciente'])
            ->where('medico_id', $medico->id)
            ->latest()
            ->paginate(15);

        return view('ui.medico.historial_clinico', [
            'registros' => $registros,
            'medico' => $medico,
        ]);
    }

    public function create(Request $request, Consulta $consulta)
    {
        $medico = Auth::user()->medico;

        if (! $medico || $consulta->medico_id !== $medico->id) {
            abort(403);
        }

        if ($consulta->estado !== 'completada') {
            return back()->withErrors(['consulta' => 'Solo se puede registrar nota clínica en consultas completadas.']);
        }

        if (HistorialClinico::where('consulta_id', $consulta->id)->exists()) {
            return redirect()->route('medico.historial.clinico.index')->with('success', 'Ya existe un registro clínico para esta consulta.');
        }

        return view('ui.medico.historial_clinico_create', [
            'consulta' => $consulta->load(['user', 'especialidad']),
            'medico' => $medico,
        ]);
    }

    public function store(Request $request, Consulta $consulta)
    {
        $medico = Auth::user()->medico;

        if (! $medico || $consulta->medico_id !== $medico->id) {
            abort(403);
        }

        if ($consulta->estado !== 'completada') {
            return back()->withErrors(['consulta' => 'Solo se puede registrar nota clínica en consultas completadas.']);
        }

        if (HistorialClinico::where('consulta_id', $consulta->id)->exists()) {
            return redirect()->route('medico.historial.clinico.index')->with('success', 'Ya existe un registro clínico para esta consulta.');
        }

        $validated = Validator::make($request->all(), [
            'nota' => ['nullable', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
        ])->validate();

        HistorialClinico::create([
            'consulta_id' => $consulta->id,
            'paciente_id' => $consulta->user_id,
            'medico_id' => $medico->id,
            'nota' => $validated['nota'] ?? null,
            'diagnostico' => $validated['diagnostico'] ?? null,
            'tratamiento' => $validated['tratamiento'] ?? null,
        ]);

        return redirect()->route('medico.historial.clinico.index')->with('success', 'Registro clínico guardado correctamente.');
    }
}
