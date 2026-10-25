@props(['messages' => []])

@if (count($messages) > 0)
    <div class="auth-errors" role="alert">
        <h3 class="auth-errors-title">Hay errores que corregir</h3>
        <ul class="auth-errors-list">
            @foreach ($messages as $message)
                <li class="auth-errors-item">{{ $message }}</li>
            @endforeach
        </ul>
    </div>
@endif