<button type="submit" href="{{ $href ?? '#' }}" {{ $attributes->class('btn btn-danger') }}>
    <i class="fa fa-minus-circle"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Delete') }}
    @endif
</button>
