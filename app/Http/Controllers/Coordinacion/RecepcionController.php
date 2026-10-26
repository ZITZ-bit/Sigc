<?php

namespace App\Http\Controllers\Coordinacion;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use Illuminate\Http\Request;

class RecepcionController extends Controller
{
    public function index(Request $request)
    {
        $query = Consulta::with(['user', 'medico.user', 'especialidad'])
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc');

        if ($request->filled('filtro')) {
            if ($request->get('filtro') === 'hoy') {
                $query->whereDate('fecha', today());
            } elseif ($request->get('filtro') === 'semana') {
                $query->whereBetween('fecha', [now()->startOfWeek(), now()->endOfWeek()]);
            }
        } else {
            $query->whereDate('fecha', today());
        }

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }

        $consultas = $query->get();

        return view('ui.coordinacion.recepcion', [
            'consultas' => $consultas,
            'filtro' => $request->get('filtro', 'hoy'),
            'filtro_estado' => $request->get('estado', ''),
        ]);
    }
}
