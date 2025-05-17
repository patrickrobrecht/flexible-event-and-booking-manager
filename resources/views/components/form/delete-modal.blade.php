@props([
    'id',
    'name',
    'route',
    'hint' => null
])
<x-bs::modal.button variant="danger" :modal="$id" class="text-nowrap">
    <i class="fa fa-fw fa-minus-circle"></i> {{ __('Delete') }}
</x-bs::modal.button>
<x-bs::modal :id="$id" :$attributes
             :close-button="false" :static-backdrop="true">
    @if(isset($hint))
        <x-bs::form.field id="hint-checkbox-{{ $id }}" name="checkbox[{{ $id }}]"
                          class="button-checkbox" check-container-class="mb-3 small"
                          type="checkbox" :options="[$hint]"
                          data-target="delete-button-{{$id}}"/>
        @once
            @push('scripts')
                <script src="{{ mix('js/enable-button-by-checkbox.js') }}"></script>
            @endpush
        @endonce
    @endif
    <x-slot:title class="fs-5">{{ __('Delete :name', ['name' => $name]) }}</x-slot:title>
    <p class="text-start">{!! __('Are you sure you want to delete :name?', ['name' => '<strong>' . $name . '</strong>']) !!}</p>
    <x-slot:footer>
        <x-bs::button variant="secondary" type="button" data-bs-dismiss="modal">
            <i class="fa fa-fw fa-window-close"></i> {{ __('Close') }}
        </x-bs::button>
        <x-bs::form method="DELETE" action="{{ $route }}">
            <x-button.delete :id="'delete-button-'.$id" :disabled="isset($hint)">{{ __('Delete (not recoverable)') }}</x-button.delete>
        </x-bs::form>
    </x-slot:footer>
</x-bs::modal>
