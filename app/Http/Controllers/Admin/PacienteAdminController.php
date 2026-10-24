<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Paciente;
use Illuminate\Http\Request;

class PacienteAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:administrador']);
    }

    public function index()
    {
        $pacientes = Paciente::with('user', 'consultas')->paginate(20);
        return view('ui.administracion.admin-portal', compact('pacientes'));
    }
}
