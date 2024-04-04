@php
    /** @var \App\Models\Document $document */
    $modalId = 'delete-document-' . $document->id;
@endphp
@can('forceDelete', $document)
    <x-bs::modal :id="$modalId" :close-button-title="__('Cancel')">
        <x-slot:title>{{ $document->title }}</x-slot:title>
        {{ __('Are you sure you want to delete :name', [
            'name' => $document->title,
        ]) }}
        <x-bs::form id="delete-form"
                    method="DELETE"
                    action="{{ route('documents.destroy', $document) }}"/>
        <x-slot:footer>
            <x-button.delete form="delete-form"/>
        </x-slot:footer>
    </x-bs::modal>
@endcan
