<?php

namespace App\Http\Controllers;

use App\Models\Consulta;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultaSyncController extends Controller
{
    /**
     * Devuelve las consultas relevantes para el usuario autenticado
     * actualizadas después de la marca de tiempo indicada.
     *
     * El filtro respeta el rol del usuario: un coordinador ve todas las
     * consultas, un médico solo las asignadas a su perfil y un paciente
     * solo sus propias solicitudes. Se utiliza para mantener sincronizadas
     * las listas sin recargar la página (polling incremental).
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Consulta::with(['user', 'medico.user', 'especialidad'])
            ->select('consultas.*');

        $roleSlug = $user->role?->slug;

        if ($roleSlug === 'medico') {
            $medico = $user->medico;
            $query->where('medico_id', $medico?->id);
        } elseif ($roleSlug === 'paciente') {
            $query->where('user_id', $user->id);
        }
        // coordinador (y cualquier otro rol autenticado) ve todas las consultas

        $since = $request->query('since');
        if ($since && Carbon::hasFormat($since, 'Y-m-d H:i:s')) {
            $query->where('updated_at', '>', Carbon::parse($since));
        } elseif ($since) {
            $query->where('updated_at', '>', Carbon::parse($since));
        }

        $consultas = $query->orderBy('updated_at', 'desc')->limit(50)->get();

        return response()
            ->json([
                'since' => now()->toDateTimeString(),
                'count' => $consultas->count(),
                'consultas' => $consultas->map(fn (Consulta $consulta): array => [
                    'id' => $consulta->id,
                    'user_id' => $consulta->user_id,
                    'medico_id' => $consulta->medico_id,
                    'estado' => $consulta->estado,
                    'fecha' => $consulta->fecha,
                    'hora' => $consulta->hora,
                    'motivo' => $consulta->motivo,
                    'updated_at' => $consulta->updated_at->toDateTimeString(),
                    'paciente' => $consulta->user?->name,
                    'medico' => $consulta->medico?->user?->name,
                    'especialidad' => $consulta->especialidad?->name,
                ])->values(),
            ])
            ->header('Cache-Control', 'no-store');
    }
}
