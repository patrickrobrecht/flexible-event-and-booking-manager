@extends('layouts.app')

@php
    /** @var \App\Models\Location $location */
@endphp

@section('title')
    {{ $location->nameOrAddress }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\Location::class)
        <x-bs::breadcrumb.item href="{{ route('locations.index') }}">{{ __('Locations') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Locations') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ $location->nameOrAddress }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $location)
        <x-button.edit href="{{ route('locations.edit', $location) }}"/>
    @endcan
    @can('forceDelete', $location)
        <x-form.delete-modal :id="$location->id"
                             :name="$location->nameOrAddress"
                             :route="route('locations.destroy', $location)"/>
    @endcan
@endsection

@section('content')
    <div class="row my-3">
        <div class="col-12 col-md-4">
            @include('locations.shared.location_details', [
                'flush' => false,
            ])
        </div>
        <div class="col-12 col-md-8">
            @canany(['viewAny', 'create'], [\App\Models\Document::class, $location])
                <section id="documents">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2><i class="fa fa-fw fa-file"></i> {{ __('Documents') }}</h2>
                        @if($location->hasImages())
                            <x-bs::button.link href="{{ route('locations.gallery', $location) }}" variant="secondary">
                                <i class="fa fa-fw fa-images"></i> {{ __('Image gallery') }}
                            </x-bs::button.link>
                        @endif
                    </div>
                    @can('viewAny', [\App\Models\Document::class, $location])
                        @include('documents.shared.document_list', [
                            'documents' => $location->documents,
                        ])
                    @endcan
                    @include('documents.shared.document_add_modal', [
                        'reference' => $location,
                        'routeForAddDocument' => route('locations.documents.store', $location),
                    ])
                </section>
            @endcanany
        </div>
    </div>

    @can('update', $location)
        <x-text.updated-human-diff :model="$location"/>
    @endcan
@endsection
