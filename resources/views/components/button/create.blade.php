<a href="{{ $href ?? '#' }}" class="btn {{ $class ?? 'btn-primary' }}">
    <i class="fa fa-plus"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Create') }}
    @endif
</a>
