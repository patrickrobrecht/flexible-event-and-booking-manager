@php
    /** @var \App\Models\User $user */
@endphp
<div id="documents" class="col-12 col-xl-6 col-xxl-4">
    <h2><i class="fa fa-fw fa-file"></i> {{ __('Documents') }}</h2>
    @if($user->documents->count() === 0)
        <x-bs::alert class="danger">{{ __(':name has not uploaded any documents yet.', [
            'name' => $user->first_name,
        ]) }}</x-bs::alert>
    @endif
    @include('documents.shared.document_list', [
        'documents' => $user->documents,
    ])
</div>
