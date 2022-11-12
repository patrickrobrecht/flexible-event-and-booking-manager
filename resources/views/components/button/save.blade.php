<button type="submit" href="{{ $href ?? '#' }}" class="btn {{ $class ?? 'btn-primary' }}">
    <i class="fa fa-save"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Save') }}
    @endif
</button>
