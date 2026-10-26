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
                                <div style="border:1px solid #e5e7eb; padding:1rem; margin-bottom:0.75rem; border-radius:8px; background:#ffffff;">
                                    <strong>{{ \Carbon\Carbon::parse($registro->consulta->fecha ?? null)->format('d/m/Y') }} {{ $registro->consulta->hora ?? '' }}</strong>
                                    <div style="margin-top:0.5rem; color:#374151;">
                                        @if ($registro->diagnostico)<div><strong>Diagnóstico:</strong> {{ $registro->diagnostico }}</div>@endif
                                        @if ($registro->nota)<div><strong>Nota:</strong> {{ $registro->nota }}</div>@endif
                                        @if ($registro->tratamiento)<div><strong>Tratamiento:</strong> {{ $registro->tratamiento }}</div>@endif
                                    </div>
                                </div>
                            @endforeach
                            <div>{{ $registros->links() }}</div>
                        @else
                            <div class="paciente-empty-state">
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
