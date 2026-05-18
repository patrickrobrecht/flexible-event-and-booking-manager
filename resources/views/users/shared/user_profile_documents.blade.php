@php
    /** @var \App\Models\User $user */
    /** @var ?string $allDocumentsLink */
@endphp
<div id="documents" class="col-12 col-xl-6 col-xxl-4">
    <h2><i class="fa fa-fw fa-file"></i><a href="{{ $allDocumentsLink }}">{{ __('Documents') }}</a></h2>
    @if($user->documents->count() === 0)
        <x-bs::alert class="danger">{{ __(':name has not uploaded any documents yet.', [
            'name' => $user->first_name,
        ]) }}</x-bs::alert>
    @else
        <div class="mb-3">
            @include('documents.shared.documents_by_status', [
                'documentsByStatus' => $documentsByStatus,
                'route' => $allDocumentsLink,
            ])
        </div>
    @endif
    @include('documents.shared.document_list', [
        'documents' => $user->documents,
    ])

    @include('documents.shared.documents_missing')
</div>
