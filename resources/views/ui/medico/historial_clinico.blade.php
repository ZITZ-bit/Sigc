<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Historial Clínico — {{ config('app.name') }}</title>
    @vite(['resources/css/medico.css', 'resources/js/app.js'])
</head>
<body>
    <div class="medico-page">
        @include('portal::medico.sidebar')
        <div class="medico-main">
            <div class="medico-content-area">
                <div class="medico-header">
                    <div class="medico-header-content">
                        <h1>Historial clínico</h1>
                        <p>Registros clínicos de consultas completadas.</p>
                    </div>
                </div>
                @if (session('success'))
                    <div class="medico-feedback medico-feedback-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="medico-feedback medico-feedback-error">{{ $errors->first() }}</div>
                @endif
                <section class="medico-section">
                    <div class="medico-historial-list">
                        @if (isset($registros) && $registros->isNotEmpty())
                            @foreach ($registros as $registro)
                                <article class="medico-historial-card">
                                    <header class="medico-historial-card__header">
                                        <div class="medico-historial-card__date">
                                            <span class="medico-historial-date__label">Consulta</span>
                                            @if ($registro->consulta?->fecha)
                                                <strong>{{ \Carbon\Carbon::parse($registro->consulta->fecha)->format('d/m/Y') }}</strong>
                                            @else
                                                <strong>Fecha no disponible</strong>
                                            @endif
                                            @if ($registro->consulta?->hora)
                                                <span>{{ substr($registro->consulta->hora, 0, 5) }}</span>
                                            @endif
                                        </div>
                                        <div class="medico-historial-patient">
                                            <span class="medico-historial-card__eyebrow">Paciente</span>
                                            <h2>{{ $registro->paciente?->name ?? 'Paciente no disponible' }}</h2>
                                            @if ($registro->consulta?->especialidad)
                                                <span class="medico-historial-specialty">{{ $registro->consulta->especialidad->name }}</span>
                                            @endif
                                        </div>
                                        <span class="medico-historial-card__status">Completada</span>
                                    </header>

                                    <div class="medico-historial-fields">
                                        @if ($registro->diagnostico)
                                            <div class="medico-historial-field medico-historial-field--diagnosis">
                                                <span class="medico-historial-field-label">Diagnóstico</span>
                                                <p>{{ $registro->diagnostico }}</p>
                                            </div>
                                        @endif
                                        @if ($registro->nota)
                                            <div class="medico-historial-field">
                                                <span class="medico-historial-field-label">Nota médica</span>
                                                <p>{{ $registro->nota }}</p>
                                            </div>
                                        @endif
                                        @if ($registro->tratamiento)
                                            <div class="medico-historial-field">
                                                <span class="medico-historial-field-label">Tratamiento</span>
                                                <p>{{ $registro->tratamiento }}</p>
                                            </div>
                                        @endif
                                        @unless ($registro->diagnostico || $registro->nota || $registro->tratamiento)
                                            <p class="medico-historial-no-details">Este registro no contiene detalles clínicos.</p>
                                        @endunless
                                    </div>
                                </article>
                                @endforeach
                        @else
                            <div class="medico-empty-state medico-historial-empty">
                                <span class="medico-historial-empty-icon" aria-hidden="true">+</span>
                                <strong>No hay registros clínicos</strong>
                                <p>Los registros de las consultas completadas aparecerán aquí.</p>
                            </div>
                        @endif
                    </div>
                    @if (isset($registros))
                        <div class="medico-historial-pagination">{{ $registros->links() }}</div>
                    @endif
                </section>
            </div>
        </div>
    </div>
</body>
</html>
