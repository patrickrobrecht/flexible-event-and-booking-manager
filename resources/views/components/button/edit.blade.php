<a href="{{ $href ?? '#' }}" class="btn {{ $class ?? 'btn-primary' }}">
    <i class="fa fa-edit"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Edit') }}
    @endif
</a>
