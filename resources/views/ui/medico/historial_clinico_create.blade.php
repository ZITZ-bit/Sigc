<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registrar Nota Clínica — {{ config('app.name') }}</title>
    @vite(['resources/css/medico.css', 'resources/js/app.js'])
</head>
<body>
    <div class="medico-page">
        @include('portal::medico.sidebar')
        <div class="medico-main">
            <div class="medico-content-area">
                <div class="medico-header">
                    <div class="medico-header-content">
                        <h1>Registrar nota clínica</h1>
                        <p>Consulta completada - Paciente: {{ $consulta->user->name ?? 'N/A' }}</p>
                    </div>
                </div>
                @if ($errors->any())
                    <div class="medico-feedback medico-feedback-error">{{ $errors->first() }}</div>
                @endif
                <section class="medico-section">
                    <form method="POST" action="{{ route('medico.historial.clinico.store', $consulta) }}">
                        @csrf
                        <div style="margin-bottom:1rem;">
                            <label>Diagnóstico</label><br>
                            <textarea name="diagnostico" rows="3" style="width:100%;"></textarea>
                        </div>
                        <div style="margin-bottom:1rem;">
                            <label>Nota clínica</label><br>
                            <textarea name="nota" rows="3" style="width:100%;"></textarea>
                        </div>
                        <div style="margin-bottom:1rem;">
                            <label>Tratamiento</label><br>
                            <textarea name="tratamiento" rows="3" style="width:100%;"></textarea>
                        </div>
                        <button type="submit" class="medico-filter-btn medico-filter-btn--active">Guardar</button>
                        <a href="{{ route('medico.consultas') }}" class="medico-filter-btn">Cancelar</a>
                    </form>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
