@props(['label', 'value', 'note', 'tone' => 'mint'])

<article class="doctor-metric tone-{{ $tone }}">
    <span>{{ $label }}</span>
    <strong>{{ $value }}</strong>
    <small>{{ $note }}</small>
</article>
