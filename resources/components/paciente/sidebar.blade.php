<nav class="paciente-sidebar" aria-label="Navegación principal del paciente">
    <div class="paciente-sidebar-header">
        <a href="{{ route('paciente.dashboard') }}" class="paciente-brand" aria-label="{{ config('app.name') }} - Panel de paciente">
            <span class="paciente-brand-icon" aria-hidden="true">&#x271a;</span>
            <span class="paciente-brand-text">{{ config('app.name') }}</span>
        </a>
    </div>

    <ul class="paciente-nav-list">
        <li>
            <a href="{{ route('paciente.dashboard') }}" class="paciente-nav-link{{ request()->routeIs('paciente.dashboard') ? ' paciente-nav-link--active' : '' }}" aria-label="Inicio">
                <span class="paciente-nav-icon" aria-hidden="true">IN</span>
                <span class="paciente-nav-label">Inicio</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.agenda') }}" class="paciente-nav-link{{ request()->routeIs('paciente.agenda') ? ' paciente-nav-link--active' : '' }}" aria-label="Mi agenda">
                <span class="paciente-nav-icon" aria-hidden="true">AG</span>
                <span class="paciente-nav-label">Mi agenda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.doctores') }}" class="paciente-nav-link{{ request()->routeIs('paciente.doctores') ? ' paciente-nav-link--active' : '' }}" aria-label="Buscar doctores">
                <span class="paciente-nav-icon" aria-hidden="true">DR</span>
                <span class="paciente-nav-label">Buscar doctores</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.consulta') }}" class="paciente-nav-link{{ request()->routeIs('paciente.consulta') ? ' paciente-nav-link--active' : '' }}" aria-label="Solicitar consulta">
                <span class="paciente-nav-icon" aria-hidden="true">CS</span>
                <span class="paciente-nav-label">Solicitar consulta</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.historial') }}" class="paciente-nav-link{{ request()->routeIs('paciente.historial') ? ' paciente-nav-link--active' : '' }}" aria-label="Historial médico">
                <span class="paciente-nav-icon" aria-hidden="true">HI</span>
                <span class="paciente-nav-label">Historial médico</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.notificaciones') }}" class="paciente-nav-link{{ request()->routeIs('paciente.notificaciones') ? ' paciente-nav-link--active' : '' }}" aria-label="Notificaciones">
                <span class="paciente-nav-icon" aria-hidden="true">AV</span>
                <span class="paciente-nav-label">Notificaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.perfil') }}" class="paciente-nav-link{{ request()->routeIs('paciente.perfil') ? ' paciente-nav-link--active' : '' }}" aria-label="Mi perfil">
                <span class="paciente-nav-icon" aria-hidden="true">PF</span>
                <span class="paciente-nav-label">Mi perfil</span>
            </a>
        </li>
    </ul>

    <div class="paciente-sidebar-footer">
        <div class="paciente-profile-summary">
            <div class="paciente-profile-avatar" aria-hidden="true">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="paciente-profile-info">
                <p class="paciente-profile-name">{{ auth()->user()->name }}</p>
                <p class="paciente-profile-role">Usuario del portal</p>
            </div>
        </div>
        <a href="{{ route('portal') }}" class="paciente-sidebar-return" aria-label="Volver al portal">&#x2190; Volver al portal</a>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="paciente-btn-logout" aria-label="Cerrar sesión" title="Cerrar sesión">
                Salir
            </button>
        </form>
    </div>
</nav>

<button class="paciente-mobile-toggle" type="button" aria-controls="paciente-mobile-sidebar" aria-expanded="false" aria-label="Abrir menú de navegación" @if (request()->routeIs('paciente.dashboard')) data-mobile-nav-toggle @endif>
    <span class="paciente-mobile-toggle-bar"></span>
    <span class="paciente-mobile-toggle-bar"></span>
    <span class="paciente-mobile-toggle-bar"></span>
</button>

<div id="paciente-mobile-sidebar" class="paciente-mobile-sidebar" role="navigation" aria-label="Menú móvil del paciente">
    <button class="paciente-mobile-close" type="button" aria-label="Cerrar menú" @if (request()->routeIs('paciente.dashboard')) data-mobile-nav-close="paciente-mobile-sidebar" @endif>Cerrar</button>
    <ul class="paciente-mobile-list">
        <li><a href="{{ route('paciente.dashboard') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.dashboard') ? ' paciente-mobile-link--active' : '' }}">Inicio</a></li>
        <li><a href="{{ route('paciente.agenda') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.agenda') ? ' paciente-mobile-link--active' : '' }}">Mi agenda</a></li>
        <li><a href="{{ route('paciente.doctores') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.doctores') ? ' paciente-mobile-link--active' : '' }}">Buscar doctores</a></li>
        <li><a href="{{ route('paciente.consulta') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.consulta') ? ' paciente-mobile-link--active' : '' }}">Solicitar consulta</a></li>
        <li><a href="{{ route('paciente.historial') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.historial') ? ' paciente-mobile-link--active' : '' }}">Historial médico</a></li>
        <li><a href="{{ route('paciente.notificaciones') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.notificaciones') ? ' paciente-mobile-link--active' : '' }}">Notificaciones</a></li>
        <li><a href="{{ route('paciente.perfil') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.perfil') ? ' paciente-mobile-link--active' : '' }}">Mi perfil</a></li>
    </ul>
    <div class="paciente-mobile-actions">
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="paciente-mobile-btn-logout" aria-label="Cerrar sesión">
                Cerrar sesión
            </button>
        </form>
    </div>
</div>

<button class="paciente-sidebar-overlay" type="button" aria-label="Cerrar menú de navegación" @if (request()->routeIs('paciente.dashboard')) data-mobile-nav-close="paciente-mobile-sidebar" @endif tabindex="-1"></button>