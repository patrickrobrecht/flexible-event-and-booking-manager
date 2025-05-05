@extends('layouts.app')

@php
    use App\Models\Material;

    /** @var Material $material */
@endphp

@section('title')
    {{ $material->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('materials.index') }}">{{ __('Materials') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $material)
        <x-button.edit href="{{ route('materials.edit', $material) }}"/>
    @endcan
    @can('forceDelete', $material)
        <x-form.delete-modal :id="$material->id"
                             :name="$material->name"
                             :route="route('materials.destroy', $material)"/>
    @endcan
@endsection

@section('content')
    @isset($material->description)
        <p class="lead">{{ $material->description }}</p>
    @endisset

    <div class="mb-3">
        <span class="text-nowrap"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}:</span>
        @can('view', $material->organization)
            <a href="{{ $material->organization->getRoute() }}">{{ $material->organization->name }}</a>
        @else
            {{ $material->organization->name }}
        @endif
    </div>

    <h2>{{ __('Storage locations') }}</h2>
    @if($material->storageLocations->isEmpty())
        <x-bs::alert variant="danger">{{ __('No storage location has been defined for the material.') }}</x-bs::alert>
    @else
        <x-bs::list>
            @foreach($material->storageLocations as $storageLocation)
                <x-bs::list.item>
                    <span>
                        <i class="fa fa-fw fa-warehouse"></i>
                        @can('view', $storageLocation)
                            <a href="{{ $storageLocation->getRoute() }}" class="fw-bold">{{ $storageLocation->name }}</a>
                        @else
                            <strong>{{ $storageLocation->name }}</strong>
                        @endcan
                    </span>
                    <div class="small">
                        <x-badge.enum :case="$storageLocation->pivot->material_status"/>
                        <span class="ms-2">{{ __('Stock') }}: {{ isset($storageLocation->pivot->stock) ? formatInt($storageLocation->pivot->stock) : __('unknown') }}</span>
                        @isset($storageLocation->pivot->remarks)
                           • {{ $storageLocation->pivot->remarks }}
                        @endisset
                    </div>
                </x-bs::list.item>
            @endforeach
        </x-bs::list>
    @endif
@endsection
