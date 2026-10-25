@php
    use Illuminate\Support\Facades\Auth;

    $panelRoute = null;
    $panelLabel = 'Panel';
    if (Auth::check() && auth()->user()->role) {
        $panelRoute = match (auth()->user()->role->slug) {
            'medico' => 'medico.dashboard',
            'coordinador' => 'coordinacion.dashboard',
            default => 'paciente.dashboard',
        };
        $panelLabel = match (auth()->user()->role->slug) {
            'medico' => 'Mi panel médico',
            'coordinador' => 'Mi panel de coordinación',
            default => 'Mi panel de paciente',
        };
    }
@endphp

<footer class="portal-footer" role="contentinfo">
    <div class="portal-footer-content">
        <div class="portal-footer-brand">
            <div style="display:flex;align-items:center;gap:0.75rem;">
                <span class="portal-footer-brand-icon" aria-hidden="true">&#x271a;</span>
                <span class="portal-footer-brand-text">{{ config('app.name') }}</span>
            </div>
            <p class="portal-footer-description">
                Clínica médica comprometida con la atención humana, el bienestar y la calidad de vida de cada paciente.
            </p>
        </div>

        <div>
            <h3 class="portal-footer-heading">Enlaces</h3>
            <div class="portal-footer-links">
                <a href="#servicios" class="portal-footer-link">Servicios</a>
                <a href="#presentacion" class="portal-footer-link">Presentación</a>
                <a href="#contacto" class="portal-footer-link">Contacto</a>
                @if (Auth::check())
                    <a href="{{ route($panelRoute) }}" class="portal-footer-link">{{ $panelLabel }}</a>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="portal-footer-link portal-footer-link--logout" aria-label="Cerrar sesión">Cerrar sesión</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="portal-footer-link">Iniciar sesión</a>
                    <a href="{{ route('register') }}" class="portal-footer-link">Registrarse</a>
                @endif
            </div>
        </div>

        <div>
            <h3 class="portal-footer-heading">Contacto</h3>
            <div class="portal-footer-actions">
                <a href="mailto:contacto@clinica.com" class="portal-footer-link">
                    &#x2709; contacto@clinica.com
                </a>
                <a href="tel:+34600000000" class="portal-footer-link">
                    &#x260e; +34 600 000 000
                </a>
                <a href="#" class="portal-footer-link">
                    &#x1F4CD; Dirección — configurar en .env
                </a>
            </div>
        </div>
    </div>

    <div class="portal-footer-bottom">
        <p class="portal-footer-copy">&copy; {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.</p>
        <div class="portal-footer-legal">
            <a href="#" class="portal-footer-link">Aviso de privacidad</a>
            <a href="#" class="portal-footer-link">Términos de uso</a>
        </div>
    </div>
</footer>
