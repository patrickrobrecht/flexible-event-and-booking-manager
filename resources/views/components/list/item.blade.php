@props([
    'flex' => true,
])
<li {{ $attributes->class(['list-group-item', 'd-flex justify-content-between align-items-center' => $flex]) }}>
    {{ $slot }}
</li>
