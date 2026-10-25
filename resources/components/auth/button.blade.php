@props(['type' => 'submit', 'label' => 'Continuar', 'class' => null, 'loading' => false, 'disabled' => false])

<button
    type="{{ $type }}"
    class="auth-button{{ $loading ? ' auth-button--loading' : '' }}{{ $class ? ' ' . $class : '' }}"
    {{ $disabled ? 'disabled' : '' }}
    {{ $loading ? 'aria-busy="true"' : '' }}
>
    @if ($loading)
        <span class="auth-button-loader" aria-hidden="true"></span>
        <span class="visually-hidden">Procesando...</span>
    @else
        {{ $label }}
    @endif
</button>