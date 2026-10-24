<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Especialidad;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrador']);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', 'unique:especialidades,nombre'],
            'descripcion' => ['nullable', 'string'],
        ]);

        Especialidad::create($validated);
        return redirect()->back()->with('success', 'Especialidad creada correctamente.');
    }
}
