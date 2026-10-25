@props(['name', 'type' => 'text', 'label', 'value' => old($name ?? null), 'required' => false, 'error' => null, 'help' => null, 'id' => null, 'autocomplete' => 'off', 'placeholder' => null])

<?php
$id = $id ?? $name;
$isInvalid = filled($error);
?>

<div class="auth-field{{ $isInvalid ? ' auth-field--error' : '' }}">
    <label for="{{ $id }}" class="auth-field-label{{ $required ? ' auth-field-label--required' : '' }}">{{ $label }}</label>

    <?php if ($type === 'password'): ?>
        <div class="auth-password-wrapper">
            <input
                id="{{ $id }}"
                name="{{ $name }}"
                type="{{ old('show_password_' . $name) ? 'text' : 'password' }}"
                value="{{ $value }}"
                {{ $required ? 'required' : '' }}
                autocomplete="{{ $autocomplete }}"
                {{ $placeholder ? 'placeholder="' . e($placeholder) . '"' : '' }}
                class="auth-input{{ $isInvalid ? ' auth-input--error' : '' }}"
                {{ $isInvalid ? 'aria-invalid="true" aria-describedby="' . $id . '-error"' : '' }}
            >
            <button
                type="button"
                class="auth-password-toggle"
                aria-label="{{ old('show_password_' . $name) ? 'Ocultar contraseña' : 'Mostrar contraseña' }}"
                aria-controls="{{ $id }}"
                data-toggle-password="{{ $id }}"
            >
                <span class="auth-password-toggle-icon" aria-hidden="true">&#x1F441;</span>
                <span class="visually-hidden">{{ old('show_password_' . $name) ? 'Ocultar' : 'Mostrar' }}</span>
            </button>
        </div>
    <?php else: ?>
        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            value="{{ $value }}"
            {{ $required ? 'required' : '' }}
            autocomplete="{{ $autocomplete }}"
            {{ $placeholder ? 'placeholder="' . e($placeholder) . '"' : '' }}
            class="auth-input{{ $isInvalid ? ' auth-input--error' : '' }}"
            {{ $isInvalid ? 'aria-invalid="true" aria-describedby="' . $id . '-error"' : '' }}
        >
    <?php endif; ?>

    @if ($help)
        <p class="auth-field-help" id="{{ $id }}-help">{{ $help }}</p>
    @endif

    @if ($error)
        <p class="auth-field-error" id="{{ $id }}-error" role="alert">{{ $error }}</p>
    @endif
</div>