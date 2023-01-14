@props([
    'vertical' => false,
])
<div {{ $attributes->class([$vertical ? 'btn-group-vertical' : 'btn-group'])->merge(['role' => 'group']) }}>
    {{ $slot }}
</div>
