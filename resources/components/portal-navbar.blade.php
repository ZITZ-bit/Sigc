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
            <a href="{{ route('login') }}" class="portal-nav-btn" aria-label="Iniciar sesión">Iniciar sesión</a>
            <a href="{{ route('register') }}" class="portal-nav-btn" aria-label="Registrarse">Registrarse</a>
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
        <a href="{{ route('login') }}" class="portal-mobile-btn" aria-label="Iniciar sesión">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="portal-mobile-btn" aria-label="Registrarse">Registrarse</a>
    </div>
</div>