@php
    /** @var \App\Models\Document $document */
    $modalId = 'delete-document-' . $document->id;
@endphp
@can('forceDelete', $document)
    <x-bs::modal.button :modal="$modalId" variant="danger">
        <i class="fa fa-minus-circle"></i> {{ __('Delete') }}
    </x-bs::modal.button>
@endcan
