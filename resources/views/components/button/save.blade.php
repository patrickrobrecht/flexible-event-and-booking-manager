<button {{ $attributes
    ->class([
        'btn',
        $class ?? 'btn-primary',
    ])
    ->merge([
        'href' => $href ?? '#',
        'type' => 'submit',
    ]) }}>
    <i class="fa fa-save"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Save') }}
    @endif
</button>
