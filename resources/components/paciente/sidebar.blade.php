<nav class="paciente-sidebar" aria-label="Navegación principal del paciente">
    <div class="paciente-sidebar-header">
        <a href="{{ route('paciente.dashboard') }}" class="paciente-brand" aria-label="{{ config('app.name') }} - Panel de paciente">
            <span class="paciente-brand-icon" aria-hidden="true">&#x271a;</span>
            <span class="paciente-brand-text">{{ config('app.name') }}</span>
        </a>
    </div>

    <ul class="paciente-nav-list" role="navigation">
        <li>
            <a href="{{ route('paciente.dashboard') }}" class="paciente-nav-link{{ request()->routeIs('paciente.dashboard') ? ' paciente-nav-link--active' : '' }}" aria-label="Inicio">
                <span class="paciente-nav-icon" aria-hidden="true">&#x1F4C5;</span>
                <span class="paciente-nav-label">Inicio</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.agenda') }}" class="paciente-nav-link{{ request()->routeIs('paciente.agenda') ? ' paciente-nav-link--active' : '' }}" aria-label="Mi agenda">
                <span class="paciente-nav-icon" aria-hidden="true">&#x1F4C5;</span>
                <span class="paciente-nav-label">Mi agenda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.doctores') }}" class="paciente-nav-link{{ request()->routeIs('paciente.doctores') ? ' paciente-nav-link--active' : '' }}" aria-label="Buscar doctores">
                <span class="paciente-nav-icon" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</span>
                <span class="paciente-nav-label">Buscar doctores</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.consulta') }}" class="paciente-nav-link{{ request()->routeIs('paciente.consulta') ? ' paciente-nav-link--active' : '' }}" aria-label="Solicitar consulta">
                <span class="paciente-nav-icon" aria-hidden="true">&#x1F4E9;</span>
                <span class="paciente-nav-label">Solicitar consulta</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.historial') }}" class="paciente-nav-link{{ request()->routeIs('paciente.historial') ? ' paciente-nav-link--active' : '' }}" aria-label="Historial médico">
                <span class="paciente-nav-icon" aria-hidden="true">&#x1F4C4;</span>
                <span class="paciente-nav-label">Historial médico</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.notificaciones') }}" class="paciente-nav-link{{ request()->routeIs('paciente.notificaciones') ? ' paciente-nav-link--active' : '' }}" aria-label="Notificaciones">
                <span class="paciente-nav-icon" aria-hidden="true">&#x1F4E7;</span>
                <span class="paciente-nav-label">Notificaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('paciente.perfil') }}" class="paciente-nav-link{{ request()->routeIs('paciente.perfil') ? ' paciente-nav-link--active' : '' }}" aria-label="Mi perfil">
                <span class="paciente-nav-icon" aria-hidden="true">&#x1F464;</span>
                <span class="paciente-nav-label">Mi perfil</span>
            </a>
        </li>
    </ul>

    <div class="paciente-sidebar-footer">
        <div class="paciente-profile-summary">
            <div class="paciente-profile-avatar" aria-hidden="true">&#x1F464;</div>
            <div class="paciente-profile-info">
                <p class="paciente-profile-name">Paciente demostrativo</p>
                <p class="paciente-profile-role">Usuario del portal</p>
            </div>
        </div>
        <a href="#" class="paciente-btn-logout" aria-label="Cerrar sesión" title="Cerrar sesión">
            &#x1F511;
        </a>
    </div>
</nav>

<button class="paciente-mobile-toggle" aria-controls="paciente-mobile-sidebar" aria-expanded="false" aria-label="Abrir menú de navegación">
    <span class="paciente-mobile-toggle-bar"></span>
    <span class="paciente-mobile-toggle-bar"></span>
    <span class="paciente-mobile-toggle-bar"></span>
</button>

<div id="paciente-mobile-sidebar" class="paciente-mobile-sidebar" role="navigation" aria-label="Menú móvil del paciente">
    <button class="paciente-mobile-close" aria-label="Cerrar menú">&#x2715;</button>
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
        <a href="#" class="paciente-mobile-btn-logout" aria-label="Cerrar sesión">Cerrar sesión</a>
    </div>
</div>