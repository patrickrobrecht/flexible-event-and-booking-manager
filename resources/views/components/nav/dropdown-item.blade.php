@props([
    'isSubItem' => false,
    'subItems' => false,
])
<li>
    <a {{ $attributes->class(['dropdown-item', 'active' => $attributes->get('href') === request()->fullUrl()]) }}>
        @if($isSubItem)
            <div class="ms-4">{{ $slot }}</div>
        @else
            {{ $slot }}
        @endif
    </a>
    @if($subItems)
        <ul class="list-unstyled small">
            {{ $subItems }}
        </ul>
    @endif
</li>
