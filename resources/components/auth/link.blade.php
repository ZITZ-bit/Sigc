@props(['label', 'url', 'text' => null])

<a href="{{ $url }}" class="auth-link">{{ $text ?? $label }}</a>