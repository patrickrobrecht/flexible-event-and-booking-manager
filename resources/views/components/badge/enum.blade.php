@php
    /** @var \App\Enums\Interfaces\MakesBadges $case */
@endphp
<x-bs::badge :variant="$case->getBadgeVariant()">
    <i class="{{ $case->getIcon() }}"></i> {{ $case->getTranslatedName() }}
</x-bs::badge>
