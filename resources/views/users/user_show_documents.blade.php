@extends('layouts.app')

@php
    use App\Enums\DocumentReferenceType;
    use App\Models\Document;
    use App\Models\User;
    use Illuminate\Pagination\LengthAwarePaginator;

    /** @var User $user */
    /** @var LengthAwarePaginator<int, Document> $documents */
@endphp

@section('title')
    {{ __('Documents by :name', [
        'name' => $user->name,
    ]) }}
@endsection

@section('breadcrumbs')
    @include('users.shared.user_breadcrumbs')
    <x-bs::breadcrumb.item>{{ __('Documents') }}</x-bs::breadcrumb.item>
@endsection

@section('content')
    @include('documents.shared.document_filters', [
        'documentReferenceTypes' => DocumentReferenceType::casesVisibleForModels($user->getVisibleDocumentReferenceTypes()),
    ])

    <x-alert.count class="mt-3" :count="$documents->total()"/>

    <div class="row my-3">
        @foreach($documents as $document)
            <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-3">
                @include('documents.shared.document_card')
            </div>
        @endforeach
    </div>

    {{ $documents->withQueryString()->links() }}
@endsection
