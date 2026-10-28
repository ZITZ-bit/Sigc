<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Coordinacion\CitasController;
use App\Http\Controllers\Coordinacion\HorariosController;
use App\Http\Controllers\Coordinacion\MedicosController;
use App\Http\Controllers\Coordinacion\PacientesController;
use App\Http\Controllers\Coordinacion\RecepcionController;
use App\Http\Controllers\Coordinacion\ReportesController;
use App\Http\Controllers\InvitacionController;
use App\Http\Controllers\Medico\CitasController as MedicoCitasController;
use App\Http\Controllers\Medico\HistorialClinicoController;
use App\Http\Controllers\NotificationToastController;
use App\Http\Controllers\Paciente\ConsultasController as PacienteConsultasController;
use App\Http\Controllers\Paciente\SolicitudConsultaController;
use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\HistorialClinico;
use App\Models\Medico;
use App\Notifications\ConsultaEstadoActualizado;
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
        $user = auth()->user();
        $consultas = Consulta::with(['medico.user', 'especialidad'])
            ->where('user_id', $user->id)
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        $proxima = $consultas->first(function ($c) {
            return in_array($c->estado, ['pendiente', 'confirmada'], true);
        }) ?? $consultas->first();

        $proximas = $consultas->take(5);
        $pendientes = $consultas->where('estado', 'pendiente')->count();
        $confirmadas = $consultas->where('estado', 'confirmada')->count();
        $notificaciones = $user->notifications()->latest()->limit(5)->get();

        return view('ui.dashboard-paciente', compact('consultas', 'proxima', 'proximas', 'pendientes', 'confirmadas', 'notificaciones'));
    })->name('paciente.dashboard');

    Route::get('/paciente/agenda', function () {
        $consultas = Consulta::with(['medico.user', 'especialidad', 'historialClinico'])
            ->where('user_id', auth()->id())
            ->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();

        return view('ui.paciente.agenda', compact('consultas'));
    })->name('paciente.agenda');

    Route::get('/paciente/doctores', function (Request $request) {
        $search = $request->query('search', '');
        $especialidades = Especialidad::orderBy('name')->get();
        $medicos = Medico::with(['user', 'especialidad'])
            ->where('estado', 'activo')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', '%'.$search.'%');
                })->orWhereHas('especialidad', function ($eq) use ($search) {
                    $eq->where('name', 'like', '%'.$search.'%');
                });
            })
            ->get();

        return view('ui.paciente.doctores', [
            'medicos' => $medicos,
            'especialidades' => $especialidades,
            'search' => $search,
        ]);
    })->name('paciente.doctores');

    Route::get('/paciente/consulta', [SolicitudConsultaController::class, 'index'])->name('paciente.consulta');
    Route::post('/paciente/consulta', [SolicitudConsultaController::class, 'store'])->name('paciente.consulta.store');
    Route::post('/paciente/agenda/{consulta}/cancelar', [PacienteConsultasController::class, 'cancelar'])->name('paciente.consultas.cancelar');
    Route::delete('/paciente/agenda/{consulta}', [PacienteConsultasController::class, 'destroy'])->name('paciente.consultas.destroy');

    Route::get('/paciente/historial-clinico', function () {
        $user = auth()->user();
        $registros = HistorialClinico::with(['consulta', 'medico.user'])
            ->where('paciente_id', $user->id)
            ->latest()
            ->paginate(15);

        return view('ui.paciente.historial_clinico', compact('registros'));
    })->name('paciente.historial.clinico');

    Route::get('/paciente/perfil', function () {
        return view('ui.paciente.perfil');
    })->name('paciente.perfil');

    Route::get('/paciente/notificaciones', function () {
        $notifications = auth()->user()->notifications()
            ->where('type', ConsultaEstadoActualizado::class)
            ->where('data->estado', 'confirmada')
            ->latest()
            ->paginate(20);

        return view('ui.paciente.notificaciones', compact('notifications'));
    })->name('paciente.notificaciones');

    Route::post('/paciente/notificaciones/{id}/leer', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notificación marcada como leída.');
    })->name('paciente.notificaciones.leer');
    Route::get('/paciente/notificaciones/nuevas', NotificationToastController::class)
        ->name('paciente.notificaciones.nuevas');
});

// Rutas protegidas del médico
Route::middleware(['auth', 'role:medico'])->group(function () {
    Route::get('/medico/dashboard', function () {
        $user = auth()->user();
        $medico = $user->medico;
        $hoy = today()->toDateString();

        $citasHoy = $medico
            ? Consulta::where('medico_id', $medico->id)->whereDate('fecha', $hoy)->count()
            : 0;
        $pendientes = $medico
            ? Consulta::where('medico_id', $medico->id)->whereDate('fecha', $hoy)->where('estado', 'pendiente')->count()
            : 0;
        $completadas = $medico
            ? Consulta::where('medico_id', $medico->id)->whereDate('fecha', $hoy)->where('estado', 'completada')->count()
            : 0;
        $proxima = $medico
            ? Consulta::with(['user', 'especialidad'])
                ->where('medico_id', $medico->id)
                ->whereIn('estado', ['pendiente', 'confirmada'])
                ->orderBy('fecha')
                ->orderBy('hora')
                ->first()
            : null;

        $notificaciones = $user->notifications()->latest()->limit(5)->get();

        return view('ui.dashboard-medico', [
            'citasHoy' => $citasHoy,
            'pendientes' => $pendientes,
            'completadas' => $completadas,
            'proxima' => $proxima,
            'notificaciones' => $notificaciones,
        ]);
    })->name('medico.dashboard');

    Route::get('/medico/agenda', [MedicoCitasController::class, 'index'])->name('medico.agenda');

    Route::get('/medico/pacientes', function (Request $request) {
        $search = $request->query('search', '');

        return view('ui.medico.pacientes', [
            'pacientes' => collect(),
            'search' => $search,
            'medico' => auth()->user()->medico,
        ]);
    })->name('medico.pacientes');

    Route::get('/medico/consultas', [MedicoCitasController::class, 'consultas'])->name('medico.consultas');
    Route::post('/medico/consultas/{consulta}/completar', [MedicoCitasController::class, 'marcarCompletada'])->name('medico.consultas.completar');

    Route::get('/medico/historial-clinico', [HistorialClinicoController::class, 'index'])->name('medico.historial.clinico.index');
    Route::get('/medico/consultas/{consulta}/historial-clinico/crear', [HistorialClinicoController::class, 'create'])->name('medico.historial.clinico.create');
    Route::post('/medico/consultas/{consulta}/historial-clinico', [HistorialClinicoController::class, 'store'])->name('medico.historial.clinico.store');

    Route::get('/medico/horario', [MedicoCitasController::class, 'horarios'])->name('medico.horario');

    Route::get('/medico/perfil', function () {
        return view('ui.medico.perfil');
    })->name('medico.perfil');

    Route::get('/medico/notificaciones', function () {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);

        return view('ui.medico.notificaciones', compact('notifications'));
    })->name('medico.notificaciones');

    Route::post('/medico/notificaciones/{id}/leer', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notificación marcada como leída.');
    })->name('medico.notificaciones.leer');
    Route::get('/medico/notificaciones/nuevas', NotificationToastController::class)
        ->name('medico.notificaciones.nuevas');
});

// Rutas protegidas del coordinador
Route::middleware(['auth', 'role:coordinador'])->group(function () {
    Route::get('/coordinacion/dashboard', function () {
        $today = today()->toDateString();
        $citasHoy = Consulta::whereDate('fecha', $today)->count();
        $pendientes = Consulta::whereDate('fecha', $today)->where('estado', 'pendiente')->count();
        $completadas = Consulta::whereDate('fecha', $today)->where('estado', 'completada')->count();
        $pacientesProgramados = Consulta::whereDate('fecha', $today)->distinct('user_id')->count('user_id');
        $medicosDisponibles = Medico::where('estado', 'activo')->count();
        $ausencias = Consulta::whereDate('fecha', $today)->where('estado', 'cancelada')->count();

        $proximas = Consulta::with(['user', 'medico.user'])
            ->whereDate('fecha', $today)
            ->orderBy('hora', 'asc')
            ->limit(10)
            ->get();

        $notificaciones = auth()->user()->notifications()->latest()->limit(10)->get();

        return view('ui.dashboard-coordinacion', [
            'citasHoy' => $citasHoy,
            'pendientes' => $pendientes,
            'completadas' => $completadas,
            'pacientesProgramados' => $pacientesProgramados,
            'medicosDisponibles' => $medicosDisponibles,
            'ausencias' => $ausencias,
            'proximas' => $proximas,
            'notificaciones' => $notificaciones,
        ]);
    })->name('coordinacion.dashboard');

    Route::get('/coordinacion/citas', [CitasController::class, 'index'])->name('coordinacion.citas');

    Route::post('/coordinacion/citas/{consulta}/asignar-medico', [CitasController::class, 'asignarMedico'])->name('coordinacion.citas.asignar-medico');

    Route::post('/coordinacion/citas/{consulta}/estado', [CitasController::class, 'cambiarEstado'])->name('coordinacion.citas.estado');
    Route::delete('/coordinacion/citas/{consulta}', [CitasController::class, 'destroy'])->name('coordinacion.citas.destroy');

    Route::get('/coordinacion/pacientes', [PacientesController::class, 'index'])->name('coordinacion.pacientes');
    Route::get('/coordinacion/pacientes/{paciente}', [PacientesController::class, 'show'])->name('coordinacion.pacientes.show');

    // Gestión de médicos (bajo "Médicos y horarios")
    Route::get('/coordinacion/medicos', [MedicosController::class, 'index'])->name('coordinacion.medicos');
    Route::get('/coordinacion/medicos/registrar', [MedicosController::class, 'create'])->name('coordinacion.medicos.registrar');
    Route::post('/coordinacion/medicos', [MedicosController::class, 'store'])->name('coordinacion.medicos.store');
    Route::get('/coordinacion/medicos/{medico}/editar', [MedicosController::class, 'edit'])->name('coordinacion.medicos.editar');
    Route::put('/coordinacion/medicos/{medico}', [MedicosController::class, 'update'])->name('coordinacion.medicos.update');
    Route::post('/coordinacion/medicos/{medico}/desactivar', [MedicosController::class, 'destroy'])->name('coordinacion.medicos.destroy');
    Route::post('/coordinacion/medicos/{medico}/activar', [MedicosController::class, 'activate'])->name('coordinacion.medicos.activate');
    Route::delete('/coordinacion/medicos/{medico}', [MedicosController::class, 'remove'])->name('coordinacion.medicos.remove');
    Route::post('/coordinacion/medicos/{medico}/invitacion', [MedicosController::class, 'sendInvitation'])->name('coordinacion.medicos.invite');

    // Gestión de horarios médicos
    Route::get('/coordinacion/medicos/{medico}/horarios', [HorariosController::class, 'index'])->name('coordinacion.medicos.horarios');
    Route::post('/coordinacion/medicos/{medico}/horarios', [HorariosController::class, 'store'])->name('coordinacion.medicos.horarios.store');
    Route::put('/coordinacion/medicos/{medico}/horarios/{horario}', [HorariosController::class, 'update'])->name('coordinacion.medicos.horarios.update');
    Route::post('/coordinacion/medicos/{medico}/horarios/{horario}/desactivar', [HorariosController::class, 'destroy'])->name('coordinacion.medicos.horarios.destroy');
    Route::delete('/coordinacion/medicos/{medico}/horarios/{horario}', [HorariosController::class, 'eliminar'])->name('coordinacion.medicos.horarios.eliminar');

    Route::get('/coordinacion/recepcion', [RecepcionController::class, 'index'])->name('coordinacion.recepcion');

    Route::get('/coordinacion/reportes', [ReportesController::class, 'index'])->name('coordinacion.reportes');

    Route::get('/coordinacion/notificaciones', function () {
        $notifications = auth()->user()->notifications()->latest()->paginate(20);

        return view('ui.coordinacion.notificaciones', compact('notifications'));
    })->name('coordinacion.notificaciones');

    Route::post('/coordinacion/notificaciones/{id}/leer', function ($id) {
        $notification = auth()->user()->notifications()->findOrFail($id);
        if (is_null($notification->read_at)) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Notificación marcada como leída.');
    })->name('coordinacion.notificaciones.leer');
    Route::get('/coordinacion/notificaciones/nuevas', NotificationToastController::class)
        ->name('coordinacion.notificaciones.nuevas');

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
