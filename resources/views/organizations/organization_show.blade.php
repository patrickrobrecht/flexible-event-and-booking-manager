@extends('layouts.app')

@php
    /** @var \App\Models\Organization $organization */
@endphp

@section('title')
    {{ $organization->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\Organization::class)
        <x-bs::breadcrumb.item href="{{ route('organizations.index') }}">{{ __('Organizations') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Organizations') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ $organization->name }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $organization)
        <x-button.edit href="{{ route('organizations.edit', $organization) }}"/>
    @endcan
@endsection

@section('content')
    <div class="row my-3">
        <div class="col-12 col-md-4">
            @include('organizations.shared.organization_details')
        </div>
        <div class="col-12 col-md-8">
            @can('viewResponsibilities', $organization)
                <section id="responsibilities">
                    <h2>{{ __('Responsibilities') }}</h2>
                    @include('users.shared.responsible_user_list', [
                        'users' => $organization->getResponsibleUsersVisibleForCurrentUser(),
                    ])
                </section>
            @endcan

            @canany(['viewAny', 'create'], [\App\Models\Document::class, $organization])
                <section id="documents" class="mt-4">
                    <h2>{{ __('Documents') }}</h2>
                    @can('viewAny', [\App\Models\Document::class, $organization])
                        @include('documents.shared.document_list', [
                            'documents' => $organization->documents,
                        ])
                    @endcan
                    @include('documents.shared.document_add_modal', [
                        'reference' => $organization,
                        'routeForAddDocument' => route('organizations.documents.store', $organization),
                    ])
                </section>
            @endcanany
        </div>
    </div>

    @can('update', $organization)
        <x-text.updated-human-diff :model="$organization"/>
    @endcan
@endsection
