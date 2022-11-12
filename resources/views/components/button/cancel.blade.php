<a href="{{ $href ?? url()->previous() }}" class="btn {{ $class ?? 'btn-secondary' }}">
    <i class="fa fa-window-close"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Cancel') }}
    @endif
</a>
