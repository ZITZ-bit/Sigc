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
                    <table class="medico-table">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Paciente</th>
                                <th>Nota/Diagnóstico/Tratamiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (isset($registros) && $registros->isNotEmpty())
                                @foreach ($registros as $registro)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($registro->consulta->fecha ?? null)->format('d/m/Y') }} {{ $registro->consulta->hora ?? '' }}</td>
                                        <td>{{ $registro->paciente->name ?? 'N/A' }}</td>
                                        <td>
                                            @if ($registro->diagnostico)<div><strong>Diagnóstico:</strong> {{ $registro->diagnostico }}</div>@endif
                                            @if ($registro->nota)<div><strong>Nota:</strong> {{ $registro->nota }}</div>@endif
                                            @if ($registro->tratamiento)<div><strong>Tratamiento:</strong> {{ $registro->tratamiento }}</div>@endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr><td colspan="3"><div class="medico-empty-state"><strong>No hay registros clínicos</strong></div></td></tr>
                            @endif
                        </tbody>
                    </table>
                    @if (isset($registros)) {{ $registros->links() }} @endif
                </section>
            </div>
        </div>
    </div>
</body>
</html>
