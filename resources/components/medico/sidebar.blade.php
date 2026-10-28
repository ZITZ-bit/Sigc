<nav class="medico-sidebar" data-dashboard-sidebar="medico" aria-label="Navegación principal del médico">
    <div class="medico-sidebar-header">
        <a href="{{ route('medico.dashboard') }}" class="medico-brand" aria-label="{{ config('app.name') }} - Panel del médico">
            <span class="medico-brand-icon" aria-hidden="true"><i data-lucide="heart-pulse"></i></span>
            <span class="medico-brand-text">{{ config('app.name') }}</span>
        </a>
        <button class="medico-sidebar-toggle" type="button" data-sidebar-toggle aria-label="Plegar menú" aria-pressed="false" title="Plegar menú"><i data-lucide="panel-left-close"></i></button>
    </div>

    <ul class="medico-nav-list">
        <li>
            <a href="{{ route('medico.dashboard') }}" class="medico-nav-link{{ request()->routeIs('medico.dashboard') ? ' medico-nav-link--active' : '' }}" aria-label="Dashboard">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="house"></i></span>
                <span class="medico-nav-label">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.agenda') }}" class="medico-nav-link{{ request()->routeIs('medico.agenda') ? ' medico-nav-link--active' : '' }}" aria-label="Mi agenda">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="calendar-days"></i></span>
                <span class="medico-nav-label">Mi agenda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.pacientes') }}" class="medico-nav-link{{ request()->routeIs('medico.pacientes') ? ' medico-nav-link--active' : '' }}" aria-label="Mis pacientes">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="users-round"></i></span>
                <span class="medico-nav-label">Mis pacientes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.consultas') }}" class="medico-nav-link{{ request()->routeIs('medico.consultas') ? ' medico-nav-link--active' : '' }}" aria-label="Consultas médicas">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="clipboard-plus"></i></span>
                <span class="medico-nav-label">Consultas médicas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.historial.clinico.index') }}" class="medico-nav-link{{ request()->routeIs('medico.historial.clinico.*') ? ' medico-nav-link--active' : '' }}" aria-label="Historial clínico">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="file-heart"></i></span>
                <span class="medico-nav-label">Historial clínico</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.horario') }}" class="medico-nav-link{{ request()->routeIs('medico.horario') ? ' medico-nav-link--active' : '' }}" aria-label="Mi horario">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="clock-3"></i></span>
                <span class="medico-nav-label">Mi horario</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.notificaciones') }}" class="medico-nav-link{{ request()->routeIs('medico.notificaciones') ? ' medico-nav-link--active' : '' }}" aria-label="Notificaciones">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="bell"></i></span>
                <span class="medico-nav-label">Notificaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.perfil') }}" class="medico-nav-link{{ request()->routeIs('medico.perfil') ? ' medico-nav-link--active' : '' }}" aria-label="Mi perfil">
                <span class="medico-nav-icon" aria-hidden="true"><i data-lucide="user-round"></i></span>
                <span class="medico-nav-label">Mi perfil</span>
            </a>
        </li>
    </ul>

    <div class="medico-sidebar-footer">
        <div class="medico-profile-summary">
            <div class="medico-profile-avatar" aria-hidden="true">{{ str(auth()->user()->name)->substr(0, 1)->upper() }}</div>
            <div class="medico-profile-info">
                <p class="medico-profile-name">{{ auth()->check() ? auth()->user()->name : 'Usuario' }}</p>
                <p class="medico-profile-role">Médico del portal</p>
            </div>
        </div>
        <a href="{{ route('portal') }}" class="medico-sidebar-return" aria-label="Volver al portal"><i data-lucide="arrow-left" aria-hidden="true"></i><span>Volver al portal</span></a>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="medico-btn-logout" aria-label="Cerrar sesión" title="Cerrar sesión">
                <i data-lucide="log-out" aria-hidden="true"></i><span>Salir</span>
            </button>
        </form>
    </div>
</nav>

<button class="medico-mobile-toggle" type="button" aria-controls="medico-mobile-sidebar" aria-expanded="false" aria-label="Abrir menú de navegación" data-mobile-nav-toggle>
    <i data-lucide="menu" aria-hidden="true"></i>
</button>

<div id="medico-mobile-sidebar" class="medico-mobile-sidebar" role="navigation" aria-label="Menú móvil del médico">
    <button class="medico-mobile-close" type="button" aria-label="Cerrar menú" data-mobile-nav-close="medico-mobile-sidebar"><i data-lucide="x" aria-hidden="true"></i></button>
    <ul class="medico-mobile-list">
        <li><a href="{{ route('medico.dashboard') }}" class="medico-mobile-link{{ request()->routeIs('medico.dashboard') ? ' medico-mobile-link--active' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('medico.agenda') }}" class="medico-mobile-link{{ request()->routeIs('medico.agenda') ? ' medico-mobile-link--active' : '' }}">Mi agenda</a></li>
        <li><a href="{{ route('medico.pacientes') }}" class="medico-mobile-link{{ request()->routeIs('medico.pacientes') ? ' medico-mobile-link--active' : '' }}">Mis pacientes</a></li>
        <li><a href="{{ route('medico.consultas') }}" class="medico-mobile-link{{ request()->routeIs('medico.consultas') ? ' medico-mobile-link--active' : '' }}">Consultas médicas</a></li>
        <li><a href="{{ route('medico.historial.clinico.index') }}" class="medico-mobile-link{{ request()->routeIs('medico.historial.clinico.*') ? ' medico-mobile-link--active' : '' }}">Historial clínico</a></li>
        <li><a href="{{ route('medico.horario') }}" class="medico-mobile-link{{ request()->routeIs('medico.horario') ? ' medico-mobile-link--active' : '' }}">Mi horario</a></li>
        <li><a href="{{ route('medico.notificaciones') }}" class="medico-mobile-link{{ request()->routeIs('medico.notificaciones') ? ' medico-mobile-link--active' : '' }}">Notificaciones</a></li>
        <li><a href="{{ route('medico.perfil') }}" class="medico-mobile-link{{ request()->routeIs('medico.perfil') ? ' medico-mobile-link--active' : '' }}">Mi perfil</a></li>
    </ul>
    <div class="medico-mobile-actions">
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="medico-mobile-btn-logout" aria-label="Cerrar sesión">
                Cerrar sesión
            </button>
        </form>
    </div>
</div>

<button class="medico-sidebar-overlay" type="button" aria-label="Cerrar menú de navegación" data-mobile-nav-close="medico-mobile-sidebar" tabindex="-1"></button>