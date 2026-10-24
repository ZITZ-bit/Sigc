@props(['label', 'value', 'note', 'icon', 'tone' => 'mint'])

<article class="admin-metric-card">
    <div class="admin-metric-top"><span>{{ $label }}</span><span class="admin-metric-icon tone-{{ $tone }}">{{ $icon }}</span></div>
    <strong class="admin-metric-value">{{ $value }}</strong>
    <span class="admin-metric-note">{{ $note }}</span>
</article>