<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:especialidades,nombre'],
            'descripcion' => ['nullable', 'string'],
        ]);

        Especialidad::create($validated);
        return redirect()->route('admin.panel', ['view' => 'equipo'])
            ->with('success', 'Especialidad creada correctamente.');
    }

    public function update(Request $request, Especialidad $especialidad)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:especialidades,nombre,' . $especialidad->id],
            'descripcion' => ['nullable', 'string'],
        ]);

        $especialidad->update($validated);

        return redirect()->route('admin.panel', ['view' => 'equipo'])
            ->with('success', 'Especialidad actualizada correctamente.');
    }
}
