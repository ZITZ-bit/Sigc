<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Pacientes — {{ config('app.name') }}</title>
    <meta name="description" content="Listado de pacientes bajo coordinación de la clínica.">
    <link rel="icon" href="/favicon.ico">
    @vite(['resources/css/coordinacion.css', 'resources/js/app.js'])
</head>
<body>

    <div class="coordinacion-page">
        @include('portal::coordinacion.sidebar')

        <div class="coordinacion-main">
            <div class="coordinacion-content-area">
                <div class="coordinacion-header">
                    <div class="coordinacion-header-content">
                        <h1>Pacientes</h1>
                        <p>Administre el listado de pacientes de la clínica.</p>
                    </div>
                </div>

                @if (session('success'))
                    <div class="coordinacion-feedback coordinacion-feedback-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="coordinacion-feedback coordinacion-feedback-error" role="alert">{{ $errors->first() }}</div>
                @endif

                <section class="coordinacion-section">
                    <div class="coordinacion-pacientes-header">
                        <form method="GET" action="{{ route('coordinacion.pacientes') }}" class="coordinacion-pacientes-search-form">
                            <input type="search" name="search" class="coordinacion-configuracion-input" placeholder="Buscar por nombre o correo..." aria-label="Buscar pacientes" value="{{ $search }}">
                            <button type="submit" class="coordinacion-filter-btn coordinacion-filter-btn--active">Buscar</button>
                            <a href="{{ route('coordinacion.pacientes') }}" class="coordinacion-filter-btn">Limpiar</a>
                        </form>
                    </div>

                    <div class="coordinacion-pacientes-grid">
                        @if ($pacientes->isEmpty())
                            <div class="coordinacion-empty-state">
                                @if ($search)
                                    <strong>No se encontraron pacientes</strong>
                                    <p>No se encontraron pacientes que coincidan con "{{ $search }}".</p>
                                @else
                                    <strong>No hay pacientes registrados</strong>
                                    <p>Los pacientes aparecerán aquí cuando se registren solicitudes de consulta.</p>
                                @endif
                            </div>
                        @else
                            <table class="coordinacion-pacientes-table">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Correo electrónico</th>
                                        <th>Fecha de registro</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pacientes as $paciente)
                                        <tr>
                                            <td>{{ $paciente->name }}</td>
                                            <td>{{ $paciente->email }}</td>
                                            <td>{{ $paciente->created_at->format('d/m/Y') }}</td>
                                            <td>
                                                <a href="{{ route('coordinacion.pacientes.show', $paciente) }}" class="coordinacion-paciente-btn coordinacion-paciente-btn--ver">Ver detalle</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{ $pacientes->appends(request()->except('page'))->links() }}
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var toggle = document.querySelector('.coordinacion-mobile-toggle');
            var close = document.querySelector('.coordinacion-mobile-close');
            var sidebar = document.getElementById('coordinacion-mobile-sidebar');
            var overlay = document.querySelector('.coordinacion-sidebar-overlay');

            if (toggle && sidebar) {
                toggle.addEventListener('click', function () {
                    sidebar.classList.toggle('open');
                    if (overlay) overlay.classList.toggle('open');
                });
            }

            if (close && sidebar) {
                close.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    if (overlay) overlay.classList.remove('open');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function () {
                    sidebar.classList.remove('open');
                    overlay.classList.remove('open');
                });
            }
        });
    </script>
</body>
</html>
