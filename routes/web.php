<?php

use App\Http\Controllers\Admin\ClinicaController;
use App\Http\Controllers\Admin\ConsultaController as AdminConsultaController;
use App\Http\Controllers\Admin\EspecialidadController;
use App\Http\Controllers\Admin\HorarioController;
use App\Http\Controllers\Admin\MedicoAdminController;
use App\Http\Controllers\Admin\PacienteAdminController;
use App\Http\Controllers\Auth\InviteController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\PacienteController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->intended(Auth::user()->redirectToPanel());
    }
    return view('portal');
});

// Autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Registro SOLO para pacientes
Route::get('/registro', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/registro', [RegisterController::class, 'register']);

// Invitaciones (médicos y administradores)
Route::get('/invitacion/{token}', [InviteController::class, 'showAcceptForm'])->name('invite.accept');
Route::post('/invitacion/{token}', [InviteController::class, 'accept']);

// Paneles protegidos
Route::middleware(['auth', 'role:paciente'])->group(function () {
    Route::get('/panel-paciente', [PacienteController::class, 'dashboard'])->name('paciente.panel');
    Route::post('/paciente/perfil', [PacienteController::class, 'updateProfile'])->name('paciente.perfil.update');
    Route::post('/paciente/solicitar-consulta', [PacienteController::class, 'solicitarConsulta'])->name('paciente.consulta.solicitar');
});

Route::middleware(['auth', 'role:medico'])->group(function () {
    Route::get('/panel-medico', [MedicoController::class, 'dashboard'])->name('medico.panel');
    Route::post('/medico/consulta/{consulta}/atender', [MedicoController::class, 'atenderConsulta'])->name('medico.consulta.atender');
    Route::post('/medico/nota', [MedicoController::class, 'crearNota'])->name('medico.nota.crear');
});

Route::middleware(['auth', 'role:administrador'])->group(function () {
    Route::get('/panel-admin', [ClinicaController::class, 'index'])->name('admin.panel');
    Route::resource('/admin/clinicas', ClinicaController::class)->except(['create', 'edit']);
    Route::resource('/admin/especialidades', EspecialidadController::class)->except(['create', 'edit', 'show']);
    Route::get('/admin/pacientes', [PacienteAdminController::class, 'index'])->name('admin.pacientes.index');
    Route::get('/admin/medicos', [MedicoAdminController::class, 'index'])->name('admin.medicos.index');
    Route::post('/admin/medicos/invitar', [MedicoAdminController::class, 'inviteMedico'])->name('admin.medicos.invitar');
    Route::post('/admin/administradores/invitar', [MedicoAdminController::class, 'inviteAdmin'])->name('admin.administradores.invitar');
    Route::get('/admin/consultas', [AdminConsultaController::class, 'index'])->name('admin.consultas.index');
    Route::post('/admin/consultas', [AdminConsultaController::class, 'store'])->name('admin.consultas.store');
    Route::put('/admin/consultas/{consulta}', [AdminConsultaController::class, 'update'])->name('admin.consultas.update');
    Route::post('/admin/horarios', [HorarioController::class, 'store'])->name('admin.horarios.store');
});
