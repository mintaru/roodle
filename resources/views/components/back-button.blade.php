@props(['url' => null, 'text' => 'Назад'])

@php
    $href = $url ?? url()->previous();
@endphp

<a href="{{ $href }}"
   class="back-button {{ $attributes->get('class') }}"
   {{ $attributes->except('class') }}
   style="display: inline-block; padding: 8px 16px; background: #e5e7eb; color: #374151; text-decoration: none; border-radius: 6px; font-size: 14px; font-weight: 500;">
    ← {{ $text }}
</a>
