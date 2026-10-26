<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="light">
    <title>Detalle de paciente — {{ config('app.name') }}</title>
    <meta name="description" content="Detalle del paciente en la clínica.">
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
                        <h1>Detalle del paciente</h1>
                        <p>Información del paciente y sus consultas registradas.</p>
                    </div>
                    <a href="{{ route('coordinacion.pacientes') }}" class="coordinacion-btn-link">Volver al listado</a>
                </div>

                @if (session('success'))
                    <div class="coordinacion-feedback coordinacion-feedback-success" role="status">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="coordinacion-feedback coordinacion-feedback-error" role="alert">{{ $errors->first() }}</div>
                @endif

                <section class="coordinacion-section">
                    <div class="coordinacion-paciente-detalle">
                        <div class="coordinacion-paciente-info">
                            <div class="coordinacion-paciente-avatar" aria-hidden="true">{{ strtoupper(substr($paciente->name, 0, 1)) }}</div>
                            <div class="coordinacion-paciente-datos">
                                <h2>{{ $paciente->name }}</h2>
                                <p class="coordinacion-paciente-email">{{ $paciente->email }}</p>
                                <p class="coordinacion-paciente-rol">{{ $paciente->role->name ?? 'Sin rol asignado' }}</p>
                                <p class="coordinacion-paciente-fecha">Registrado: {{ $paciente->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="coordinacion-paciente-consultas">
                        <h3 class="coordinacion-section-title">Consultas registradas</h3>
                        @if ($consultas->isEmpty())
                            <div class="coordinacion-empty-state">
                                <strong>No hay consultas registradas</strong>
                                <p>Este paciente no tiene solicitudes de consulta en el sistema.</p>
                            </div>
                        @else
                            <table class="coordinacion-pacientes-table">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Hora</th>
                                        <th>Especialidad</th>
                                        <th>Motivo</th>
                                        <th>Estado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($consultas as $consulta)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($consulta->fecha)->format('d/m/Y') }}</td>
                                            <td>{{ $consulta->hora }}</td>
                                            <td>{{ $consulta->especialidad?->name ?: 'Sin especialidad' }}</td>
                                            <td>{{ $consulta->motivo ?: 'Sin motivo registrado' }}</td>
                                            <td>
                                                <span class="coordinacion-badge coordinacion-badge--{{ $consulta->estado }}">{{ ucfirst($consulta->estado) }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
