@props([
    'variant' => 'text-success',
    'text' => __('Currently saving.'),
])
<div wire:loading.block {{ $attributes->class([
    'mt-3',
    $variant,
]) }}>
    <div class="spinner-border ms-auto"></div>
    <strong role="status">{{ $text }}</strong>
</div>
