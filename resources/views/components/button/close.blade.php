<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="{{ __('Close') }}">
    <i class="fa fa-window-close"></i>
    @if(trim($slot))
        {{ $slot }}
    @else
        {{ __('Close') }}
    @endif
</button>
