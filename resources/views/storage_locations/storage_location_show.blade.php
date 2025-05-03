@extends('layouts.app')

@section('title')
    {{ $storageLocation->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('storage-locations.index') }}">{{ __('Storage locations') }}</x-bs::breadcrumb.item>
    @foreach($storageLocation->getAncestors() as $parentStorageLocation)
        <x-bs::breadcrumb.item href="{{ $parentStorageLocation->getRoute() }}">{{ $parentStorageLocation->name }}</x-bs::breadcrumb.item>
    @endforeach
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $storageLocation)
        <x-button.edit href="{{ route('storage-locations.edit', $storageLocation) }}"/>
    @endcan
    @can('forceDelete', $storageLocation)
        <x-form.delete-modal :id="$storageLocation->id"
                             :name="$storageLocation->name"
                             :route="route('storage-locations.destroy', $storageLocation)"/>
    @endcan
@endsection

@section('content')
    @isset($storageLocation->description)
        <p class="lead">{{ $storageLocation->description }}</p>
    @endisset

    @if($storageLocation->materials->isNotEmpty())
        <h2>{{ __('Materials') }}</h2>
        <x-bs::list>
            @foreach($storageLocation->materials as $material)
                <x-bs::list.item>
                    <span>
                        <i class="fa fa-fw fa-toolbox"></i>
                        @can('view', $material)
                            <a href="{{ $material->getRoute() }}" class="fw-bold">{{ $material->name }}</a>
                        @else
                            <strong>{{ $material->name }}</strong>
                        @endcan
                    </span>
                    <div class="small">
                        <x-badge.enum :case="$material->pivot->material_status"/>
                        <span class="ms-2">{{ __('Stock') }}: {{ isset($material->pivot->stock) ? formatInt($material->pivot->stock) : __('unknown') }}</span>
                        @isset($material->pivot->remarks)
                            • {{ $material->pivot->remarks }}
                        @endisset
                    </div>
                </x-bs::list.item>
            @endforeach
        </x-bs::list>
    @elseif($storageLocation->childStorageLocations->isEmpty())
        <h2>{{ __('Materials') }}</h2>
        <x-bs::alert variant="danger">{{ __('This storage location does not contain any materials yet.') }}</x-bs::alert>
    @endif

    @if($storageLocation->childStorageLocations->isNotEmpty())
        <h2>{{ __('Child storage locations') }}</h2>
        <x-bs::list class="my-3">
            @include('storage_locations.shared.storage_location_list_items', [
                'marginLevel' => 0,
                'storageLocations' => $storageLocation->childStorageLocations,
            ])
        </x-bs::list>
    @endif
@endsection
