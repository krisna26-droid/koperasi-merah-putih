@props(['value', 'class' => '', 'showSign' => false])

@php
    $amount = (float) $value;
    $color = $amount < 0 ? '#dc2626' : ($amount > 0 ? '#16a34a' : '#1e293b');
@endphp

<span {{ $attributes->merge(['class' => "font-mono {$class}"]) }} style="color: {{ $color }}">
    Rp {{ $amount > 0 && $showSign ? '+' : '' }}{{ number_format($amount, 0, ',', '.') }}
</span>
