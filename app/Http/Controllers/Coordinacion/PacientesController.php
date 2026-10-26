<?php

namespace App\Http\Controllers\Coordinacion;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PacientesController extends Controller
{
    /**
     * Listar usuarios con rol paciente.
     */
    public function index(Request $request)
    {
        $pacienteRole = Role::where('slug', 'paciente')->first();

        if (!$pacienteRole) {
            return view('ui.coordinacion.pacientes', [
                'pacientes' => collect(),
                'search' => $request->get('search', ''),
            ]);
        }

        $query = User::with('role')
            ->where('role_id', $pacienteRole->id)
            ->orderBy('created_at', 'desc');

        // Búsqueda por nombre o correo
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pacientes = $query->paginate(10);

        return view('ui.coordinacion.pacientes', [
            'pacientes' => $pacientes,
            'search' => $request->get('search', ''),
        ]);
    }

    /**
     * Mostrar detalle de un paciente.
     */
    public function show(Request $request, User $paciente)
    {
        $pacienteRole = Role::where('slug', 'paciente')->first();

        if (!$pacienteRole || $paciente->role_id !== $pacienteRole->id) {
            abort(404, 'El usuario solicitado no es un paciente.');
        }

        $consultas = \App\Models\Consulta::with('especialidad')
            ->where('user_id', $paciente->id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->limit(5)
            ->get();

        return view('ui.coordinacion.pacientes-show', [
            'paciente' => $paciente,
            'consultas' => $consultas,
        ]);
    }
}
