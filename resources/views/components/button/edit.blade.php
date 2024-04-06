<a href="{{ $href ?? '#' }}" {{ $attributes->class('btn btn-primary') }}>
    <i class="fa fa-edit"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Edit') }}
    @endif
</a>
