<li class="nav-item">
    <a {{ $attributes->class(['nav-link', 'active' => $attributes->get('href') === request()->url()]) }}>
        {{ $slot }}
    </a>
</li>
