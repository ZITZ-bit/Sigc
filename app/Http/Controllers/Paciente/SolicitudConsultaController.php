<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use App\Models\Especialidad;
use App\Models\Medico;
use App\Models\User;
use App\Notifications\ConsultaSolicitada;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SolicitudConsultaController extends Controller
{
    public function index(Request $request): View
    {
        $fecha = $request->query('fecha', old('fecha'));
        $especialidadSlug = $request->query('especialidad', old('especialidad'));
        $horasDisponibles = [];

        if (is_string($fecha) && is_string($especialidadSlug) && $fecha !== '' && $especialidadSlug !== '') {
            $filters = Validator::make(
                ['fecha' => $fecha, 'especialidad' => $especialidadSlug],
                [
                    'fecha' => ['required', 'date_format:Y-m-d'],
                    'especialidad' => ['required', 'exists:especialidades,slug'],
                ],
            )->validated();
            $horasDisponibles = $this->horasDisponibles($filters['fecha'], $filters['especialidad']);
        }

        return view('ui.paciente.consulta', [
            'especialidades' => Especialidad::query()->orderBy('name')->get(),
            'fechaSeleccionada' => $fecha,
            'fechaSeleccionadaEsHoy' => $fecha === now()->toDateString(),
            'especialidadSeleccionada' => $especialidadSlug,
            'horasDisponibles' => $horasDisponibles,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'especialidad' => ['required', 'string', 'exists:especialidades,slug'],
            'fecha' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'hora' => ['required', 'date_format:H:i'],
            'motivo' => ['nullable', 'string', 'max:1000'],
        ]);

        $fechaHora = Carbon::createFromFormat('Y-m-d H:i', $validated['fecha'].' '.$validated['hora']);

        if ($fechaHora->lessThanOrEqualTo(now())) {
            throw ValidationException::withMessages([
                'hora' => 'Selecciona una hora futura para la fecha elegida.',
            ]);
        }

        $horasDisponibles = $this->horasDisponibles($validated['fecha'], $validated['especialidad']);

        if (! in_array($validated['hora'], $horasDisponibles, true)) {
            throw ValidationException::withMessages([
                'hora' => 'Ese horario ya no está disponible. Elige otra hora disponible.',
            ]);
        }

        $especialidad = Especialidad::where('slug', $validated['especialidad'])->firstOrFail();

        $consulta = Consulta::create([
            'user_id' => $request->user()->id,
            'medico_id' => null,
            'especialidad_id' => $especialidad->id,
            'fecha' => $validated['fecha'],
            'hora' => $validated['hora'],
            'motivo' => $validated['motivo'] ?? null,
            'estado' => 'pendiente',
        ]);

        $consulta->load(['user', 'especialidad']);

        User::query()
            ->whereHas('role', fn ($query) => $query->where('slug', 'coordinador'))
            ->get()
            ->each(fn (User $coordinador) => $coordinador->notify(new ConsultaSolicitada($consulta)));

        return redirect()
            ->route('paciente.agenda')
            ->with('success', '¡Solicitud enviada correctamente! Coordinación podrá revisarla en Recepción y seguimiento.');
    }

    /**
     * @return list<string>
     */
    private function horasDisponibles(string $fecha, string $especialidadSlug): array
    {
        $dia = Carbon::createFromFormat('Y-m-d', $fecha);
        $especialidad = Especialidad::where('slug', $especialidadSlug)->first();

        if (! $especialidad) {
            return [];
        }

        $medicos = Medico::query()
            ->where('estado', 'activo')
            ->where('especialidad_id', $especialidad->id)
            ->whereHas('horarios', fn ($query) => $query
                ->where('dia_semana', $dia->dayOfWeek)
                ->where('activo', true))
            ->with(['horarios' => fn ($query) => $query
                ->where('dia_semana', $dia->dayOfWeek)
                ->where('activo', true)])
            ->get(['id']);

        if ($medicos->isEmpty()) {
            return [];
        }

        $citasOcupadas = Consulta::query()
            ->whereIn('medico_id', $medicos->modelKeys())
            ->whereDate('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'confirmada'])
            ->get(['medico_id', 'hora'])
            ->map(fn (Consulta $consulta): string => $consulta->medico_id.'|'.Carbon::parse($consulta->hora)->format('H:i'))
            ->all();

        $horasDelDia = array_map(static fn (int $hora): string => sprintf('%02d:00', $hora), range(0, 23));

        return array_values(array_filter($horasDelDia, function (string $hora) use ($fecha, $medicos, $citasOcupadas): bool {
            if (Carbon::createFromFormat('Y-m-d H:i', $fecha.' '.$hora)->lessThanOrEqualTo(now())) {
                return false;
            }

            return $medicos->contains(function (Medico $medico) use ($hora, $citasOcupadas): bool {
                $horaNormalizada = Carbon::createFromFormat('H:i', $hora)->format('H:i:s');

                if (in_array($medico->id.'|'.$hora, $citasOcupadas, true)) {
                    return false;
                }

                return $medico->horarios->contains(fn ($horario): bool => $horaNormalizada >= Carbon::parse($horario->hora_inicio)->format('H:i:s')
                    && $horaNormalizada < Carbon::parse($horario->hora_fin)->format('H:i:s'));
            });
        }));
    }
}
