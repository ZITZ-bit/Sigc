@props([
    'href' => '#inicio',
    'label' => 'SIGC, inicio',
    'variant' => 'default',
])

@php
    $brandClasses = $variant === 'footer'
        ? 'gap-[7px] text-[16px] text-[var(--ink)]'
        : 'gap-2.5 text-[21px] max-[760px]:gap-[7px] max-[760px]:text-[18px]';
    $markSize = $variant === 'footer'
        ? 'h-[23px] w-[23px]'
        : 'h-7 w-7 max-[760px]:h-[25px] max-[760px]:w-[25px]';
@endphp

<a {{ $attributes->merge(['class' => "inline-flex items-center font-bold tracking-[0] transition-colors duration-200 hover:text-[var(--coral)] {$brandClasses}"]) }} href="{{ $href }}" aria-label="{{ $label }}">
    <span class="inline-flex items-end gap-[3px] rounded-[9px_9px_9px_3px] bg-[var(--green)] p-[5px] {{ $markSize }}" aria-hidden="true">
        <span class="block h-2 w-1 rounded-[4px] bg-[#d4e8b0]"></span>
        <span class="block h-[13px] w-1 rounded-[4px] bg-[#d4e8b0]"></span>
        <span class="block h-[17px] w-1 rounded-[4px] bg-[#d4e8b0]"></span>
    </span>
    <span>SIGC</span>
</a>