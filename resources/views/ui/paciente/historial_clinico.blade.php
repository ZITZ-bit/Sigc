<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mi Historial Clínico — {{ config('app.name') }}</title>
    @vite(['resources/css/paciente.css', 'resources/js/app.js'])
</head>
<body>
    <div class="paciente-page">
        @include('portal::paciente.sidebar')
        <div class="paciente-main">
            <div class="paciente-content-area">
                <div class="paciente-header">
                    <div class="paciente-header-content">
                        <h1>Mi historial clínico</h1>
                        <p>Registros clínicos asociados a tus consultas completadas.</p>
                    </div>
                </div>
                <section class="paciente-section">
                    <div class="paciente-historial-list">
                        @if (isset($registros) && $registros->isNotEmpty())
                            @foreach ($registros as $registro)
                                <article class="paciente-historial-item">
                                    <header class="paciente-historial-item__header">
                                        <div class="paciente-historial-date">
                                            @if ($registro->consulta?->fecha)
                                                <span class="paciente-historial-date-value">{{ \Carbon\Carbon::parse($registro->consulta->fecha)->format('d/m/Y') }}</span>
                                            @else
                                                <span class="paciente-historial-date-value">Fecha no disponible</span>
                                            @endif
                                            @if ($registro->consulta?->hora)
                                                <span class="paciente-historial-time">{{ substr($registro->consulta->hora, 0, 5) }}</span>
                                            @endif
                                        </div>
                                        <div class="paciente-historial-info">
                                            <span class="paciente-historial-type">Registro de consulta</span>
                                            <strong class="paciente-historial-doctor">{{ $registro->medico?->user?->name ?? 'Médico no disponible' }}</strong>
                                            @if ($registro->consulta?->especialidad)
                                                <span class="paciente-historial-specialty">{{ $registro->consulta->especialidad->name }}</span>
                                            @endif
                                        </div>
                                        <span class="paciente-historial-status">Consulta atendida</span>
                                    </header>

                                    <div class="paciente-historial-fields">
                                        @if ($registro->diagnostico)
                                            <div class="paciente-historial-field paciente-historial-field--diagnosis">
                                                <span class="paciente-historial-field-label">Diagnóstico</span>
                                                <p>{{ $registro->diagnostico }}</p>
                                            </div>
                                        @endif
                                        @if ($registro->nota)
                                            <div class="paciente-historial-field">
                                                <span class="paciente-historial-field-label">Nota médica</span>
                                                <p>{{ $registro->nota }}</p>
                                            </div>
                                        @endif
                                        @if ($registro->tratamiento)
                                            <div class="paciente-historial-field">
                                                <span class="paciente-historial-field-label">Tratamiento</span>
                                                <p>{{ $registro->tratamiento }}</p>
                                            </div>
                                        @endif
                                        @unless ($registro->diagnostico || $registro->nota || $registro->tratamiento)
                                            <p class="paciente-historial-no-details">No se añadieron detalles clínicos a este registro.</p>
                                        @endunless
                                    </div>
                                </article>
                            @endforeach
                            <div class="paciente-historial-pagination">{{ $registros->links() }}</div>
                        @else
                            <div class="paciente-empty-state paciente-historial-empty">
                                <span class="paciente-historial-empty-icon" aria-hidden="true">+</span>
                                <strong>No tienes registros clínicos</strong>
                                <p>Los registros aparecerán aquí cuando tu médico los registre tras una consulta completada.</p>
                            </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
