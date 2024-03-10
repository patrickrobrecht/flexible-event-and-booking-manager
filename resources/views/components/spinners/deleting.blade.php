@props([
    'variant' => 'text-danger',
    'text' => __('Currently deleting.'),
])
<div wire:loading.block {{ $attributes->class([
    'mt-3',
    $variant,
]) }}>
    <div class="spinner-border ms-auto"></div>
    <strong role="status">{{ $text }}</strong>
</div>
