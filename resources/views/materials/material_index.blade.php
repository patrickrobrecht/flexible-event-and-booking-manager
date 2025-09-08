@extends('layouts.app')

@php
    use App\Models\Material;
    use App\Models\Organization;
    use App\Models\StorageLocation;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Portavice\Bladestrap\Support\Options;

    /** @var LengthAwarePaginator<Material> $materials */
    /** @var Collection<Organization> $organizations */
@endphp

@section('title')
    {{ __('Materials') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    @can('create', Material::class)
        <x-bs::button.link href="{{ route('materials.create') }}" class="d-print-none">
            <i class="fa fa-fw fa-plus"></i> {{ __('Create material') }}
        </x-bs::button.link>
    @endcan

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="description" name="filter[description]" type="text"
                                  :from-query="true">{{ __('Description') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="organization_id" name="filter[organization_id]" type="select" :options="Options::fromModels($organizations, 'name')->prepend(__('all'), \App\Enums\FilterValue::All->value)"
                                  :from-query="true"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="material_status" name="filter[material_status]"
                                  type="select" :options="\App\Enums\MaterialStatus::toOptionsWithAll()"
                                  :cast="\App\Enums\FilterValue::castToIntIfNoValue()"
                                  :from-query="true">{{ __('Status') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="storage_location_id" name="filter[storage_location_id]" type="select"
                                  :options="Options::fromArray(StorageLocation::filterOptions())"
                                  :from-query="true"><i class="fa fa-fw fa-warehouse"></i> {{ __('Storage locations') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Material::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>

        <x-slot:addButtons>
            <x-bs::button type="submit" name="output" value="export">
                <i class="fa fa-fw fa-download"></i> {{ __('Export') }}
            </x-bs::button>
        </x-slot:addButtons>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$materials->total()"/>

    <div class="row my-3">
        @foreach($materials as $material)
            <div class="col-12 col-md-6 col-xxl-4 mb-3">
                <div class="card avoid-break">
                    <div class="card-header">
                        <h2 class="card-title">
                            @can('view', $material)
                                <a href="{{ $material->getRoute() }}">{{ $material->name }}</a>
                            @else
                                {{ $material->name }}
                            @endcan
                        </h2>
                    </div>
                    <x-bs::list :flush="true">
                        @isset($material->description)
                            <x-bs::list.item class="text-muted">{{ $material->description }}</x-bs::list.item>
                        @endisset
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</span>
                            <x-slot:end>
                                <div class="text-end">
                                    @can('view', $material->organization)
                                        <a href="{{ $material->organization->getRoute() }}">{{ $material->organization->name }}</a>
                                    @else
                                        {{ $material->organization->name }}
                                    @endif
                                </div>
                            </x-slot:end>
                        </x-bs::list.item>
                        @if($material->storageLocations->isEmpty())
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-warehouse"></i>
                                <span class="text-danger">{{ __('No storage location has been defined for the material.') }}</span>
                            </x-bs::list.item>
                        @else
                            @foreach($material->storageLocations as $storageLocation)
                                <x-bs::list.item>
                                    <span class="nowrap">
                                        <i class="fa fa-fw fa-warehouse"></i>
                                        @can('view', $storageLocation)
                                            <a href="{{ $storageLocation->getRoute() }}">{{ $storageLocation->name }}</a>
                                        @else
                                            {{ $storageLocation->name }}
                                        @endif

                                    </span>
                                    <x-slot:end>
                                        <x-badge.enum :case="$storageLocation->pivot->material_status"/>
                                    </x-slot:end>
                                </x-bs::list.item>
                            @endforeach
                        @endif
                    </x-bs::list>
                    @canany(['update', 'forceDelete'], $material)
                        <div class="card-body d-print-none">
                            @can('update', $material)
                                <x-button.edit href="{{ route('materials.edit', $material) }}"/>
                            @endcan
                            @can('forceDelete', $material)
                                <x-form.delete-modal :id="$material->id"
                                                     :name="$material->name"
                                                     :route="route('materials.destroy', $material)"/>
                            @endcan
                        </div>
                    @endcanany
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$material"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $materials->withQueryString()->links() }}
@endsection
