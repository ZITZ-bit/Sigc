<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use App\Models\Nota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicoController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $medico = $user->medico()->with('especialidad', 'clinica', 'horarios')->first();
        $consultas = Consulta::with('paciente.user')
            ->where('medico_id', $medico->id)
            ->orderBy('fecha_hora')
            ->get();
        $notasPendientes = Nota::with('paciente.user')
            ->where('medico_id', $medico->id)
            ->where('firmada', false)
            ->get();
        $notasFirmadas = Nota::with('paciente.user')
            ->where('medico_id', $medico->id)
            ->where('firmada', true)
            ->orderBy('firmada_at', 'desc')
            ->take(5)
            ->get();

        return view('ui.medicos.medico-portal', compact('user', 'medico', 'consultas', 'notasPendientes', 'notasFirmadas'));
    }

    public function atenderConsulta(Request $request, Consulta $consulta)
    {
        $this->authorize('update', $consulta);
        $consulta->update(['estado' => 'completada']);
        return redirect()->back()->with('success', 'Consulta marcada como atendida.');
    }

    public function crearNota(Request $request)
    {
        $user = Auth::user();
        $medico = $user->medico;

        $validated = $request->validate([
            'paciente_id' => ['required', 'exists:pacientes,id'],
            'consulta_id' => ['nullable', 'exists:consultas,id'],
            'contenido' => ['required', 'string'],
            'diagnostico' => ['nullable', 'string'],
            'tratamiento' => ['nullable', 'string'],
        ]);

        Nota::create([
            'medico_id' => $medico->id,
            'paciente_id' => $validated['paciente_id'],
            'consulta_id' => $validated['consulta_id'] ?? null,
            'contenido' => $validated['contenido'],
            'diagnostico' => $validated['diagnostico'],
            'tratamiento' => $validated['tratamiento'],
            'firmada' => false,
        ]);

        return redirect()->back()->with('success', 'Nota clínica guardada.');
    }
}
