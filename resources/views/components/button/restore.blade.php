<button type="submit" href="{{ $href ?? '#' }}" {{ $attributes->class('btn btn-success') }}>
    <i class="fa fa-plus-circle"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Restore') }}
    @endif
</button>
