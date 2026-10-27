<nav class="paciente-sidebar" data-dashboard-sidebar="paciente" aria-label="Navegación principal del paciente">
    <div class="paciente-sidebar-header">
        <a href="{{ route('paciente.dashboard') }}" class="paciente-brand" aria-label="{{ config('app.name') }} - Panel de paciente">
            <span class="paciente-brand-icon" aria-hidden="true"><i data-lucide="heart-pulse"></i></span>
            <span class="paciente-brand-text">{{ config('app.name') }}</span>
        </a>
        <button class="paciente-sidebar-toggle" type="button" data-sidebar-toggle aria-label="Plegar menú" aria-pressed="false" title="Plegar menú"><i data-lucide="panel-left-close"></i></button>
    </div>

    <ul class="paciente-nav-list">
        <li>
            <a href="{{ route('paciente.dashboard') }}" class="paciente-nav-link{{ request()->routeIs('paciente.dashboard') ? ' paciente-nav-link--active' : '' }}" aria-label="Inicio">
                <span class="paciente-nav-icon" aria-hidden="true"><i data-lucide="house"></i></span>
                <span class="paciente-nav-label">Inicio</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.agenda') }}" class="paciente-nav-link{{ request()->routeIs('paciente.agenda') ? ' paciente-nav-link--active' : '' }}" aria-label="Mi agenda">
                <span class="paciente-nav-icon" aria-hidden="true"><i data-lucide="calendar-days"></i></span>
                <span class="paciente-nav-label">Mi agenda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.doctores') }}" class="paciente-nav-link{{ request()->routeIs('paciente.doctores') ? ' paciente-nav-link--active' : '' }}" aria-label="Buscar doctores">
                <span class="paciente-nav-icon" aria-hidden="true"><i data-lucide="stethoscope"></i></span>
                <span class="paciente-nav-label">Buscar doctores</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.consulta') }}" class="paciente-nav-link{{ request()->routeIs('paciente.consulta') ? ' paciente-nav-link--active' : '' }}" aria-label="Solicitar consulta">
                <span class="paciente-nav-icon" aria-hidden="true"><i data-lucide="clipboard-plus"></i></span>
                <span class="paciente-nav-label">Solicitar consulta</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.historial.clinico') }}" class="paciente-nav-link{{ request()->routeIs('paciente.historial.clinico') ? ' paciente-nav-link--active' : '' }}" aria-label="Historial médico">
                <span class="paciente-nav-icon" aria-hidden="true"><i data-lucide="file-heart"></i></span>
                <span class="paciente-nav-label">Historial médico</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.notificaciones') }}" class="paciente-nav-link{{ request()->routeIs('paciente.notificaciones') ? ' paciente-nav-link--active' : '' }}" aria-label="Notificaciones">
                <span class="paciente-nav-icon" aria-hidden="true"><i data-lucide="bell"></i></span>
                <span class="paciente-nav-label">Notificaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.perfil') }}" class="paciente-nav-link{{ request()->routeIs('paciente.perfil') ? ' paciente-nav-link--active' : '' }}" aria-label="Mi perfil">
                <span class="paciente-nav-icon" aria-hidden="true"><i data-lucide="user-round"></i></span>
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
        <a href="{{ route('portal') }}" class="paciente-sidebar-return" aria-label="Volver al portal"><i data-lucide="arrow-left" aria-hidden="true"></i><span>Volver al portal</span></a>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="paciente-btn-logout" aria-label="Cerrar sesión" title="Cerrar sesión">
                <i data-lucide="log-out" aria-hidden="true"></i><span>Salir</span>
            </button>
        </form>
    </div>
</nav>

<button class="paciente-mobile-toggle" type="button" aria-controls="paciente-mobile-sidebar" aria-expanded="false" aria-label="Abrir menú de navegación" data-mobile-nav-toggle>
    <i data-lucide="menu" aria-hidden="true"></i>
</button>

<div id="paciente-mobile-sidebar" class="paciente-mobile-sidebar" role="navigation" aria-label="Menú móvil del paciente">
    <button class="paciente-mobile-close" type="button" aria-label="Cerrar menú" data-mobile-nav-close="paciente-mobile-sidebar"><i data-lucide="x" aria-hidden="true"></i></button>
    <ul class="paciente-mobile-list">
        <li><a href="{{ route('paciente.dashboard') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.dashboard') ? ' paciente-mobile-link--active' : '' }}">Inicio</a></li>
        <li><a href="{{ route('paciente.agenda') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.agenda') ? ' paciente-mobile-link--active' : '' }}">Mi agenda</a></li>
        <li><a href="{{ route('paciente.doctores') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.doctores') ? ' paciente-mobile-link--active' : '' }}">Buscar doctores</a></li>
        <li><a href="{{ route('paciente.consulta') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.consulta') ? ' paciente-mobile-link--active' : '' }}">Solicitar consulta</a></li>
        <li><a href="{{ route('paciente.historial.clinico') }}" class="paciente-mobile-link{{ request()->routeIs('paciente.historial.clinico') ? ' paciente-mobile-link--active' : '' }}">Historial médico</a></li>
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

<button class="paciente-sidebar-overlay" type="button" aria-label="Cerrar menú de navegación" data-mobile-nav-close="paciente-mobile-sidebar" tabindex="-1"></button>