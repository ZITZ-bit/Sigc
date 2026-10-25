<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response|RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userRole = $user->role?->slug;

        if (! in_array($userRole, $roles)) {
            // Redirect to their own dashboard or return 403
            return redirect()->route($this->dashboardRouteForUser($user));
        }

        return $next($request);
    }

    /**
     * Obtiene el nombre de la ruta del dashboard según el rol del usuario.
     */
    private function dashboardRouteForUser($user): string
    {
        return match ($user->role?->slug) {
            'medico' => 'medico.dashboard',
            'coordinador' => 'coordinacion.dashboard',
            default => 'paciente.dashboard',
        };
    }
}
