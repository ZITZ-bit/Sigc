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
    return view('ui.dashboard');
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

// Rutas del proyecto sigc