<nav class="medico-sidebar" aria-label="Navegación principal del médico">
    <div class="medico-sidebar-header">
        <a href="{{ route('medico.dashboard') }}" class="medico-brand" aria-label="{{ config('app.name') }} - Panel del médico">
            <span class="medico-brand-icon" aria-hidden="true">&#x271a;</span>
            <span class="medico-brand-text">{{ config('app.name') }}</span>
        </a>
    </div>

    <ul class="medico-nav-list" role="navigation">
        <li>
            <a href="{{ route('medico.dashboard') }}" class="medico-nav-link{{ request()->routeIs('medico.dashboard') ? ' medico-nav-link--active' : '' }}" aria-label="Dashboard">
                <span class="medico-nav-icon" aria-hidden="true">&#x1F4C5;</span>
                <span class="medico-nav-label">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.agenda') }}" class="medico-nav-link{{ request()->routeIs('medico.agenda') ? ' medico-nav-link--active' : '' }}" aria-label="Mi agenda">
                <span class="medico-nav-icon" aria-hidden="true">&#x1F4C5;</span>
                <span class="medico-nav-label">Mi agenda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.pacientes') }}" class="medico-nav-link{{ request()->routeIs('medico.pacientes') ? ' medico-nav-link--active' : '' }}" aria-label="Mis pacientes">
                <span class="medico-nav-icon" aria-hidden="true">&#x1F464;</span>
                <span class="medico-nav-label">Mis pacientes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.consultas') }}" class="medico-nav-link{{ request()->routeIs('medico.consultas') ? ' medico-nav-link--active' : '' }}" aria-label="Consultas médicas">
                <span class="medico-nav-icon" aria-hidden="true">&#x1F4E9;</span>
                <span class="medico-nav-label">Consultas médicas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.historial') }}" class="medico-nav-link{{ request()->routeIs('medico.historial') ? ' medico-nav-link--active' : '' }}" aria-label="Historial clínico">
                <span class="medico-nav-icon" aria-hidden="true">&#x1F4C4;</span>
                <span class="medico-nav-label">Historial clínico</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.horario') }}" class="medico-nav-link{{ request()->routeIs('medico.horario') ? ' medico-nav-link--active' : '' }}" aria-label="Mi horario">
                <span class="medico-nav-icon" aria-hidden="true">&#x23F0;</span>
                <span class="medico-nav-label">Mi horario</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.notificaciones') }}" class="medico-nav-link{{ request()->routeIs('medico.notificaciones') ? ' medico-nav-link--active' : '' }}" aria-label="Notificaciones">
                <span class="medico-nav-icon" aria-hidden="true">&#x1F4E7;</span>
                <span class="medico-nav-label">Notificaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('medico.perfil') }}" class="medico-nav-link{{ request()->routeIs('medico.perfil') ? ' medico-nav-link--active' : '' }}" aria-label="Mi perfil">
                <span class="medico-nav-icon" aria-hidden="true">&#x1F464;</span>
                <span class="medico-nav-label">Mi perfil</span>
            </a>
        </li>
    </ul>

    <div class="medico-sidebar-footer">
        <div class="medico-profile-summary">
            <div class="medico-profile-avatar" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</div>
            <div class="medico-profile-info">
                <p class="medico-profile-name">Dr. Médico demostrativo</p>
                <p class="medico-profile-role">Médico del portal</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="medico-btn-logout" aria-label="Cerrar sesión" title="Cerrar sesión">
                &#x1F511;
            </button>
        </form>
    </div>
</nav>

<button class="medico-mobile-toggle" aria-controls="medico-mobile-sidebar" aria-expanded="false" aria-label="Abrir menú de navegación">
    <span class="medico-mobile-toggle-bar"></span>
    <span class="medico-mobile-toggle-bar"></span>
    <span class="medico-mobile-toggle-bar"></span>
</button>

<div id="medico-mobile-sidebar" class="medico-mobile-sidebar" role="navigation" aria-label="Menú móvil del médico">
    <button class="medico-mobile-close" aria-label="Cerrar menú">&#x2715;</button>
    <ul class="medico-mobile-list">
        <li><a href="{{ route('medico.dashboard') }}" class="medico-mobile-link{{ request()->routeIs('medico.dashboard') ? ' medico-mobile-link--active' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('medico.agenda') }}" class="medico-mobile-link{{ request()->routeIs('medico.agenda') ? ' medico-mobile-link--active' : '' }}">Mi agenda</a></li>
        <li><a href="{{ route('medico.pacientes') }}" class="medico-mobile-link{{ request()->routeIs('medico.pacientes') ? ' medico-mobile-link--active' : '' }}">Mis pacientes</a></li>
        <li><a href="{{ route('medico.consultas') }}" class="medico-mobile-link{{ request()->routeIs('medico.consultas') ? ' medico-mobile-link--active' : '' }}">Consultas médicas</a></li>
        <li><a href="{{ route('medico.historial') }}" class="medico-mobile-link{{ request()->routeIs('medico.historial') ? ' medico-mobile-link--active' : '' }}">Historial clínico</a></li>
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