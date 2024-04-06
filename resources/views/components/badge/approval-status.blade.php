@props([
    'approvalStatus',
])
@php
    /** @var \App\Options\ApprovalStatus $approvalStatus */
@endphp
<x-bs::badge variant="{{ $approvalStatus->getBadgeVariant() }}" {{ $attributes }}>
    <i class="{{ $approvalStatus->getIcon() }}"></i> {{ $approvalStatus->getTranslatedName() }}
</x-bs::badge>
