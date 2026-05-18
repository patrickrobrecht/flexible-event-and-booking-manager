@extends('layouts.app')

@php
    use App\Enums\DocumentReferenceType;
    use App\Models\Document;
    use App\Models\User;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Facades\Auth;

    /** @var LengthAwarePaginator<int, Document> $documents */

    /** @var User $loggedInUser */
    $loggedInUser = Auth::user();
@endphp

@section('title')
    {{ __('My documents') }}
@endsection

@section('breadcrumbs')
    @can('viewAccount', User::class)
        <x-bs::breadcrumb.item href="{{ route('account.show') }}">{{ __('My account') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ __('My documents') }}</x-bs::breadcrumb.item>
@endsection

@section('content')
    @include('documents.shared.document_filters', [
        'documentReferenceTypes' => DocumentReferenceType::casesVisibleForModels($loggedInUser->getVisibleDocumentReferenceTypes()),
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
