<?php

use App\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'portal')->name('portal');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/paciente/dashboard', function () {
    return view('ui.dashboard-paciente');
})->name('paciente.dashboard');

Route::get('/paciente/agenda', function () {
    return view('ui.paciente.agenda');
})->name('paciente.agenda');

Route::get('/paciente/doctores', function () {
    return view('ui.paciente.doctores');
})->name('paciente.doctores');

Route::get('/paciente/consulta', function () {
    return view('ui.paciente.consulta');
})->name('paciente.consulta');

Route::get('/paciente/historial', function () {
    return view('ui.paciente.historial');
})->name('paciente.historial');

Route::get('/paciente/perfil', function () {
    return view('ui.paciente.perfil');
})->name('paciente.perfil');

Route::get('/paciente/notificaciones', function () {
    return view('ui.paciente.notificaciones');
})->name('paciente.notificaciones');

Route::get('/medico/dashboard', function () {
    return view('ui.dashboard-medico');
})->name('medico.dashboard');

Route::get('/medico/agenda', function () {
    return view('ui.medico.agenda');
})->name('medico.agenda');

Route::get('/medico/pacientes', function () {
    return view('ui.medico.pacientes');
})->name('medico.pacientes');

Route::get('/medico/consultas', function () {
    return view('ui.medico.consultas');
})->name('medico.consultas');

Route::get('/medico/historial', function () {
    return view('ui.medico.historial');
})->name('medico.historial');

Route::get('/medico/horario', function () {
    return view('ui.medico.horario');
})->name('medico.horario');

Route::get('/medico/perfil', function () {
    return view('ui.medico.perfil');
})->name('medico.perfil');

Route::get('/medico/notificaciones', function () {
    return view('ui.medico.notificaciones');
})->name('medico.notificaciones');

// Rutas del proyecto sigc