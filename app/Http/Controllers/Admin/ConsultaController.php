<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ConsultaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrador']);
    }

    public function index()
    {
        $consultas = Consulta::with('paciente.user', 'medico.user', 'clinica')->orderBy('fecha_hora', 'desc')->get();
        $medicos = Medico::with('user')->get();
        $pacientes = Paciente::with('user')->get();
        return view('ui.administracion.admin-portal', compact('consultas', 'medicos', 'pacientes'));
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

        $medico = Medico::findOrFail($validated['medico_id']);

        Consulta::create([
            'paciente_id' => $validated['paciente_id'],
            'medico_id' => $validated['medico_id'],
            'clinica_id' => $medico->clinica_id,
            'fecha_hora' => $validated['fecha_hora'],
            'motivo' => $validated['motivo'] ?? null,
            'estado' => $validated['estado'] ?? 'confirmada',
        ]);

        return redirect()->back()->with('success', 'Consulta creada correctamente.');
    }

    public function update(Request $request, Consulta $consulta)
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:pendiente,confirmada,completada,cancelada'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $consulta->update($validated);
        return redirect()->back()->with('success', 'Consulta actualizada.');
    }
}
