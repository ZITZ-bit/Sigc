<?php

namespace App\Http\Controllers\Coordinacion;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Medico;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
        ]);

        $desde = $request->filled('desde') ? Carbon::parse($request->get('desde'))->startOfDay() : null;
        $hasta = $request->filled('hasta') ? Carbon::parse($request->get('hasta'))->endOfDay() : null;

        $baseQuery = Consulta::query();
        if ($desde) {
            $baseQuery->whereDate('fecha', '>=', $desde->toDateString());
        }
        if ($hasta) {
            $baseQuery->whereDate('fecha', '<=', $hasta->toDateString());
        }

        $totales = [
            'total' => (clone $baseQuery)->count(),
            'pendiente' => (clone $baseQuery)->where('estado', 'pendiente')->count(),
            'confirmada' => (clone $baseQuery)->where('estado', 'confirmada')->count(),
            'completada' => (clone $baseQuery)->where('estado', 'completada')->count(),
            'cancelada' => (clone $baseQuery)->where('estado', 'cancelada')->count(),
        ];

        $porEspecialidad = (clone $baseQuery)
            ->selectRaw('especialidad_id, COUNT(*) as total')
            ->groupBy('especialidad_id')
            ->with('especialidad')
            ->get()
            ->map(function ($row) {
                return [
                    'especialidad' => $row->especialidad?->name ?? 'Sin especialidad',
                    'total' => $row->total,
                ];
            });

        $porMedico = (clone $baseQuery)
            ->selectRaw('medico_id, COUNT(*) as total')
            ->whereNotNull('medico_id')
            ->groupBy('medico_id')
            ->with('medico.user')
            ->get()
            ->map(function ($row) {
                return [
                    'medico' => $row->medico?->user?->name ?? 'Sin asignar',
                    'total' => $row->total,
                ];
            });

        $pacientesActivos = User::whereHas('role', function ($q) {
            $q->where('slug', 'paciente');
        })->count();

        $medicosActivos = Medico::where('estado', 'activo')->count();

        return view('ui.coordinacion.reportes', [
            'totales' => $totales,
            'porEspecialidad' => $porEspecialidad,
            'porMedico' => $porMedico,
            'pacientesActivos' => $pacientesActivos,
            'medicosActivos' => $medicosActivos,
            'desde' => $request->get('desde'),
            'hasta' => $request->get('hasta'),
        ]);
    }
}
