@props([
    'number',
    'label',
    'visualLabel',
    'visualTone' => 'agenda',
    'title',
    'description',
])

@php
    $visualBackground = [
        'agenda' => 'bg-[#e2eee5]',
        'records' => 'bg-[#f8e8dc]',
        'insights' => 'bg-[#e2edf0]',
    ][$visualTone] ?? 'bg-[#e2eee5]';
@endphp

<article {{ $attributes->merge(['class' => 'min-w-0']) }}>
    <div class="relative grid h-[230px] place-items-center overflow-hidden rounded-[4px] max-[760px]:h-[250px] max-[520px]:h-[225px] {{ $visualBackground }}">
        <span class="absolute left-[18px] top-[17px] text-[8px] font-bold tracking-[.7px] text-[#6c7c72]">{{ $visualLabel }}</span>
        {{ $visual }}
    </div>
    <p class="mb-2.5 mt-6 text-[8px] font-bold tracking-[.8px] text-[#819087] max-[760px]:mt-[18px]">{{ $number }} / {{ $label }}</p>
    <h3 class="m-0 mb-[10px] max-w-[310px] text-[17px] leading-[1.35] text-[#293e34]">{{ $title }}</h3>
    <p class="m-0 max-w-[340px] text-[12px] leading-[1.75] text-[var(--muted)]">{{ $description }}</p>
</article>