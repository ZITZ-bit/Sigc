<nav class="coordinacion-sidebar" aria-label="Navegación principal de coordinación">
    <div class="coordinacion-sidebar-header">
        <a href="{{ route('coordinacion.dashboard') }}" class="coordinacion-brand" aria-label="{{ config('app.name') }} - Coordinación Clínica">
            <span class="coordinacion-brand-icon" aria-hidden="true">&#x271a;</span>
            <span class="coordinacion-brand-text">Coordinación</span>
        </a>
    </div>

    <ul class="coordinacion-nav-list" role="navigation">
        <li>
            <a href="{{ route('coordinacion.dashboard') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.dashboard') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Dashboard">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F4C5;</span>
                <span class="coordinacion-nav-label">Dashboard</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.citas') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.citas') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Citas y agenda">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F4C5;</span>
                <span class="coordinacion-nav-label">Citas y agenda</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.pacientes') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.pacientes') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Pacientes">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F464;</span>
                <span class="coordinacion-nav-label">Pacientes</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.medicos') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.medicos') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Médicos y horarios">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F468;&#x200D;&#x1F3EB;</span>
                <span class="coordinacion-nav-label">Médicos y horarios</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.recepcion') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.recepcion') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Recepción y seguimiento">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F464;</span>
                <span class="coordinacion-nav-label">Recepción y seguimiento</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.reportes') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.reportes') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Reportes y estadísticas">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F4CA;</span>
                <span class="coordinacion-nav-label">Reportes y estadísticas</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.notificaciones') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.notificaciones') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Notificaciones">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F4E7;</span>
                <span class="coordinacion-nav-label">Notificaciones</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.perfil') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.perfil') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Mi perfil">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F464;</span>
                <span class="coordinacion-nav-label">Mi perfil</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.personal') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.personal') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Personal y usuarios">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x1F465;</span>
                <span class="coordinacion-nav-label">Personal y usuarios</span>
            </a>
        </li>
        <li>
            <a href="{{ route('coordinacion.configuracion') }}" class="coordinacion-nav-link{{ request()->routeIs('coordinacion.configuracion') ? ' coordinacion-nav-link--active' : '' }}" aria-label="Configuración de la clínica">
                <span class="coordinacion-nav-icon" aria-hidden="true">&#x2699;&#xFE0F;</span>
                <span class="coordinacion-nav-label">Configuración de la clínica</span>
            </a>
        </li>
    </ul>

    <div class="coordinacion-sidebar-footer">
        <div class="coordinacion-profile-summary">
            <div class="coordinacion-profile-avatar" aria-hidden="true">&#x1F465;</div>
            <div class="coordinacion-profile-info">
                <p class="coordinacion-profile-name">Coordinador demostrativo</p>
                <p class="coordinacion-profile-role">Coordinación clínica</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="coordinacion-btn-logout" aria-label="Cerrar sesión" title="Cerrar sesión">
                &#x1F511;
            </button>
        </form>
    </div>
</nav>

<button class="coordinacion-mobile-toggle" aria-controls="coordinacion-mobile-sidebar" aria-expanded="false" aria-label="Abrir menú de navegación">
    <span class="coordinacion-mobile-toggle-bar"></span>
    <span class="coordinacion-mobile-toggle-bar"></span>
    <span class="coordinacion-mobile-toggle-bar"></span>
</button>

<div id="coordinacion-mobile-sidebar" class="coordinacion-mobile-sidebar" role="navigation" aria-label="Menú móvil de coordinación">
    <button class="coordinacion-mobile-close" aria-label="Cerrar menú">&#x2715;</button>
    <ul class="coordinacion-mobile-list">
        <li><a href="{{ route('coordinacion.dashboard') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.dashboard') ? ' coordinacion-mobile-link--active' : '' }}">Dashboard</a></li>
        <li><a href="{{ route('coordinacion.citas') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.citas') ? ' coordinacion-mobile-link--active' : '' }}">Citas y agenda</a></li>
        <li><a href="{{ route('coordinacion.pacientes') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.pacientes') ? ' coordinacion-mobile-link--active' : '' }}">Pacientes</a></li>
        <li><a href="{{ route('coordinacion.medicos') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.medicos') ? ' coordinacion-mobile-link--active' : '' }}">Médicos y horarios</a></li>
        <li><a href="{{ route('coordinacion.recepcion') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.recepcion') ? ' coordinacion-mobile-link--active' : '' }}">Recepción y seguimiento</a></li>
        <li><a href="{{ route('coordinacion.reportes') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.reportes') ? ' coordinacion-mobile-link--active' : '' }}">Reportes y estadísticas</a></li>
        <li><a href="{{ route('coordinacion.notificaciones') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.notificaciones') ? ' coordinacion-mobile-link--active' : '' }}">Notificaciones</a></li>
        <li><a href="{{ route('coordinacion.perfil') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.perfil') ? ' coordinacion-mobile-link--active' : '' }}">Mi perfil</a></li>
        <li><a href="{{ route('coordinacion.personal') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.personal') ? ' coordinacion-mobile-link--active' : '' }}">Personal y usuarios</a></li>
        <li><a href="{{ route('coordinacion.configuracion') }}" class="coordinacion-mobile-link{{ request()->routeIs('coordinacion.configuracion') ? ' coordinacion-mobile-link--active' : '' }}">Configuración</a></li>
    </ul>
    <div class="coordinacion-mobile-actions">
        <form method="POST" action="{{ route('logout') }}" style="display: inline;">
            @csrf
            <button type="submit" class="coordinacion-mobile-btn-logout" aria-label="Cerrar sesión">
                Cerrar sesión
            </button>
        </form>
    </div>
</div>