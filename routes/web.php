<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Coordinacion\MedicosController;
use App\Http\Controllers\InvitacionController;
use App\Models\Consulta;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::view('/', 'portal')->name('portal');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Rutas públicas de activación de médico (sin autenticación)
Route::get('/medico/activar/{token}', [InvitacionController::class, 'show'])->name('medico.activar');
Route::post('/medico/activar/{token}', [InvitacionController::class, 'activate'])->name('medico.activar.post');

// Rutas protegidas del paciente
Route::middleware(['auth', 'role:paciente'])->group(function () {
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

    Route::post('/paciente/consulta', function (Request $request) {
        $validated = $request->validate([
            'especialidad' => ['required', 'string'],
            'fecha' => ['required', 'date'],
            'hora' => ['required', 'string'],
            'motivo' => ['nullable', 'string', 'max:1000'],
        ]);

        // Validar que la combinación de fecha y hora sea futura
        $fechaHora = \Carbon\Carbon::parse($validated['fecha'] . ' ' . $validated['hora']);
        if ($fechaHora->lte(now())) {
            return back()->withErrors([
                'fecha' => 'La fecha y hora deben ser posteriores al momento actual.',
            ])->withInput();
        }

        // Resolver especialidad por slug desde la tabla real
        $especialidad = Especialidad::where('slug', $validated['especialidad'])->first();

        $consulta = new Consulta([
            'fecha' => $validated['fecha'],
            'hora' => $validated['hora'],
            'motivo' => $validated['motivo'] ?? null,
        ]);
        $consulta->user_id = auth()->id();
        $consulta->medico_id = null;
        $consulta->especialidad_id = $especialidad?->id;
        $consulta->estado = 'pendiente';
        $consulta->save();

        return redirect()
            ->route('paciente.agenda')
            ->with('success', 'Tu solicitud de consulta ha sido registrada correctamente.');
    })->name('paciente.consulta.store');

    Route::get('/paciente/historial', function () {
        return view('ui.paciente.historial');
    })->name('paciente.historial');

    Route::get('/paciente/perfil', function () {
        return view('ui.paciente.perfil');
    })->name('paciente.perfil');

    Route::get('/paciente/notificaciones', function () {
        return view('ui.paciente.notificaciones');
    })->name('paciente.notificaciones');
});

// Rutas protegidas del médico
Route::middleware(['auth', 'role:medico'])->group(function () {
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
});

// Rutas protegidas del coordinador
Route::middleware(['auth', 'role:coordinador'])->group(function () {
    Route::get('/coordinacion/dashboard', function () {
        return view('ui.dashboard-coordinacion');
    })->name('coordinacion.dashboard');

    Route::get('/coordinacion/citas', function () {
        return view('ui.coordinacion.citas');
    })->name('coordinacion.citas');

    Route::get('/coordinacion/pacientes', function () {
        return view('ui.coordinacion.pacientes');
    })->name('coordinacion.pacientes');

    // Gestión de médicos (bajo "Médicos y horarios")
    Route::get('/coordinacion/medicos', [MedicosController::class, 'index'])->name('coordinacion.medicos');
    Route::get('/coordinacion/medicos/registrar', [MedicosController::class, 'create'])->name('coordinacion.medicos.registrar');
    Route::post('/coordinacion/medicos', [MedicosController::class, 'store'])->name('coordinacion.medicos.store');
    Route::get('/coordinacion/medicos/{medico}/editar', [MedicosController::class, 'edit'])->name('coordinacion.medicos.editar');
    Route::put('/coordinacion/medicos/{medico}', [MedicosController::class, 'update'])->name('coordinacion.medicos.update');
    Route::post('/coordinacion/medicos/{medico}/desactivar', [MedicosController::class, 'destroy'])->name('coordinacion.medicos.destroy');
    Route::post('/coordinacion/medicos/{medico}/activar', [MedicosController::class, 'activate'])->name('coordinacion.medicos.activate');
    Route::post('/coordinacion/medicos/{medico}/invitacion', [MedicosController::class, 'sendInvitation'])->name('coordinacion.medicos.invite');

    Route::get('/coordinacion/recepcion', function () {
        return view('ui.coordinacion.recepcion');
    })->name('coordinacion.recepcion');

    Route::get('/coordinacion/reportes', function () {
        return view('ui.coordinacion.reportes');
    })->name('coordinacion.reportes');

    Route::get('/coordinacion/notificaciones', function () {
        return view('ui.coordinacion.notificaciones');
    })->name('coordinacion.notificaciones');

    Route::get('/coordinacion/perfil', function () {
        return view('ui.coordinacion.perfil');
    })->name('coordinacion.perfil');

    Route::get('/coordinacion/personal', function () {
        return view('ui.coordinacion.personal');
    })->name('coordinacion.personal');

    Route::get('/coordinacion/configuracion', function () {
        return view('ui.coordinacion.configuracion');
    })->name('coordinacion.configuracion');
});

// Rutas del proyecto sigc
