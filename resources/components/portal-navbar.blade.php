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

<nav class="portal-navbar" aria-label="Navegación principal">
    <div class="portal-navbar-inner">
        <a href="{{ route('portal') }}" class="portal-brand" aria-label="{{ config('app.name') }} - Inicio">
            <span class="portal-brand-icon" aria-hidden="true">&#x271a;</span>
            <span class="portal-brand-text">{{ config('app.name') }}</span>
        </a>

        <div class="portal-nav">
            <a href="#servicios" class="portal-nav-link">Servicios</a>
            <a href="#presentacion" class="portal-nav-link">Presentación</a>
            <a href="#contacto" class="portal-nav-link">Contacto</a>
        </div>

        <div class="portal-nav-actions">
            @if (Auth::check())
                <a href="{{ route($panelRoute) }}" class="portal-nav-btn" aria-label="{{ $panelLabel }}">{{ $panelLabel }}</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;" aria-label="Cerrar sesión">
                    @csrf
                    <button type="submit" class="portal-nav-btn portal-nav-btn--logout" aria-label="Cerrar sesión" title="Cerrar sesión">
                        &#x1F6AA; Cerrar sesión
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="portal-nav-btn" aria-label="Iniciar sesión">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="portal-nav-btn-primary" aria-label="Registrarse">Registrarse</a>
            @endif
        </div>

        <button class="portal-mobile-toggle" aria-controls="portal-mobile-menu" aria-expanded="false" aria-label="Abrir menú">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>

<div id="portal-mobile-menu" class="portal-mobile-menu" role="navigation" aria-label="Menú móvil">
    <a href="#servicios" class="portal-mobile-link">Servicios</a>
    <a href="#presentacion" class="portal-mobile-link">Presentación</a>
    <a href="#contacto" class="portal-mobile-link">Contacto</a>
    <div class="portal-mobile-actions">
        @if (Auth::check())
            <a href="{{ route($panelRoute) }}" class="portal-mobile-link">{{ $panelLabel }}</a>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;" aria-label="Cerrar sesión">
                @csrf
                <button type="submit" class="portal-mobile-btn portal-mobile-btn--logout" aria-label="Cerrar sesión">
                    &#x1F6AA; Cerrar sesión
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="portal-mobile-btn" aria-label="Iniciar sesión">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="portal-mobile-btn portal-mobile-btn--primary" aria-label="Registrarse">Registrarse</a>
        @endif
    </div>
</div>
