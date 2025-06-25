@php
    /** @var \App\Models\Traits\HasDocuments $reference */
    /** @var string $routeForAddDocument */
@endphp
@can('create', [\App\Models\Document::class, $reference])
    <x-bs::modal.button modal="add-document-modal" variant="primary" @class([
        'mt-3' => $reference->documents->isNotEmpty(),
        'd-print-none',
    ])>
        <i class="fa fa-fw fa-plus"></i> {{ __('Add document') }}
    </x-bs::modal.button>
    <x-bs::modal id="add-document-modal" :close-button-title="__('Cancel')" class="modal-xl">
        <x-slot:title>{{ __('Add document') }}</x-slot:title>
        <x-bs::form id="add-document-form"
                    method="POST" action="{{ $routeForAddDocument }}"
                    enctype="multipart/form-data">
            @include('documents.shared.document_form_fields', [
                'document' => null,
            ])
        </x-bs::form>
        <x-slot:footer>
            <x-bs::button form="add-document-form">
                <i class="fa fa-fw fa-plus"></i> {{ __('Add document') }}
            </x-bs::button>
        </x-slot:footer>
    </x-bs::modal>
    @if($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                (new bootstrap.Modal(document.getElementById('add-document-modal'))).show();
            });
        </script>
    @endif
@endcan
