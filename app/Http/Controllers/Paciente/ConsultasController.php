<?php

namespace App\Http\Controllers\Paciente;

use App\Http\Controllers\Controller;
use App\Models\Consulta;
use Illuminate\Http\RedirectResponse;

class ConsultasController extends Controller
{
    public function cancelar(int $consultaId): RedirectResponse
    {
        $consulta = Consulta::query()
            ->where('user_id', auth()->id())
            ->findOrFail($consultaId);

        if (! in_array($consulta->estado, ['pendiente', 'confirmada'], true)) {
            return back()->withErrors([
                'consulta' => 'Solo puedes cancelar una cita pendiente o confirmada.',
            ]);
        }

        $consulta->update(['estado' => 'cancelada']);

        return redirect()
            ->route('paciente.agenda')
            ->with('success', 'La cita se canceló correctamente.');
    }

    public function destroy(int $consultaId): RedirectResponse
    {
        $consulta = Consulta::query()
            ->where('user_id', auth()->id())
            ->findOrFail($consultaId);

        if ($consulta->historialClinico()->exists()) {
            return back()->withErrors([
                'consulta' => 'No se puede eliminar esta cita porque tiene un historial clínico asociado.',
            ]);
        }

        $consulta->delete();

        return redirect()
            ->route('paciente.agenda')
            ->with('success', 'La cita se eliminó correctamente.');
    }
}
