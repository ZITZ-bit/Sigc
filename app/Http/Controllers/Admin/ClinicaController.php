<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clinica;
use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Horario;
use App\Models\Medico;
use App\Models\Paciente;
use Illuminate\Http\Request;

class ClinicaController extends Controller
{
    public function index()
    {
        $clinicas = Clinica::withCount('medicos')->orderBy('nombre')->get();
        $pacientes = Paciente::with(['user', 'consultas' => fn ($query) => $query->latest('fecha_hora')])
            ->withCount('consultas')
            ->latest()
            ->get();
        $medicos = Medico::with(['user', 'especialidad', 'clinica'])
            ->withCount('consultas')
            ->orderBy('id')
            ->get();
        $especialidades = Especialidad::withCount('medicos')->orderBy('nombre')->get();
        $consultas = Consulta::with(['paciente.user', 'medico.user', 'medico.especialidad', 'clinica'])
            ->orderBy('fecha_hora')
            ->get();
        $horarios = Horario::with(['medico.user', 'medico.especialidad'])
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio')
            ->get();
        $consultasHoy = $consultas->filter(fn (Consulta $consulta) => $consulta->fecha_hora->isToday());

        $resumen = [
            'pacientes_activos' => $pacientes->filter(fn (Paciente $paciente) => $paciente->user?->activo)->count(),
            'consultas_hoy' => $consultasHoy->count(),
            'pendientes' => $consultas->where('estado', 'pendiente')->count(),
            'medicos' => $medicos->filter(fn (Medico $medico) => $medico->user?->activo)->count(),
            'especialidades' => $especialidades->count(),
        ];
        $agendaData = $consultas->map(fn (Consulta $consulta) => [
            'id' => $consulta->id,
            'start' => $consulta->fecha_hora->toIso8601String(),
            'date' => $consulta->fecha_hora->toDateString(),
            'time' => $consulta->fecha_hora->format('H:i'),
            'end' => $consulta->fecha_hora->copy()->addMinutes($consulta->duracion_minutos)->format('H:i'),
            'patient' => $consulta->paciente?->user?->name ?? 'Paciente',
            'doctor' => $consulta->medico?->user?->name ?? 'Médico',
            'doctorId' => $consulta->medico_id,
            'specialty' => $consulta->medico?->especialidad?->nombre ?? 'Sin especialidad',
            'room' => $consulta->medico?->consultorio ?? 'Sin consultorio',
            'status' => $consulta->estado,
        ])->values();
        $formOptions = [
            'patients' => $pacientes->map(fn (Paciente $paciente) => [
                'id' => $paciente->id,
                'label' => trim(($paciente->user?->name ?? 'Paciente') . ' ' . $paciente->user?->apellido),
            ])->values(),
            'doctors' => $medicos->map(fn (Medico $medico) => [
                'id' => $medico->id,
                'label' => trim(($medico->user?->name ?? 'Médico') . ' ' . $medico->user?->apellido . ' · ' . ($medico->especialidad?->nombre ?? 'Sin especialidad')),
            ])->values(),
            'specialties' => $especialidades->map(fn (Especialidad $especialidad) => [
                'id' => $especialidad->id,
                'label' => $especialidad->nombre,
            ])->values(),
            'clinics' => $clinicas->map(fn (Clinica $clinica) => [
                'id' => $clinica->id,
                'label' => $clinica->nombre,
            ])->values(),
        ];

        $user = auth()->user();

        return view('ui.administracion.admin-portal', compact(
            'clinicas',
            'pacientes',
            'medicos',
            'especialidades',
            'consultas',
            'consultasHoy',
            'horarios',
            'resumen',
            'agendaData',
            'formOptions',
            'user',
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:255'],
        ]);

        Clinica::create($validated);
        return redirect()->route('admin.panel')->with('success', 'Clínica creada correctamente.');
    }
}
