@props([
    'case',
])
@php
    /** @var \App\Enums\Interfaces\MakesBadges $case */
@endphp
<x-bs::badge :variant="$case->getBadgeVariant()" {{ $attributes }}>
    <i class="{{ $case->getIcon() }}"></i> {{ $case->getTranslatedName() }}
</x-bs::badge>
