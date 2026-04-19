@extends('layouts.app')

@php
    use App\Models\StorageLocation;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Portavice\Bladestrap\Support\ValueHelper;

    /** @var LengthAwarePaginator<StorageLocation> $storageLocations */
@endphp

@section('title')
    {{ __('Storage locations') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    @can('create', StorageLocation::class)
        <x-bs::button.link href="{{ route('storage-locations.create') }}" class="d-print-none">
            <i class="fa fa-fw fa-plus"></i> {{ __('Create storage location') }}
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
            <div class="col-12 col-lg-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="[StorageLocation::HIERARCHICAL => __('hierarchical'), ...StorageLocation::sortOptions()->getNamesWithLabels()]"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>

        <x-slot:addButtons>
            <x-bs::button type="submit" name="output" value="export">
                <i class="fa fa-fw fa-download"></i> {{ __('Export') }}
            </x-bs::button>
        </x-slot:addButtons>
    </x-form.filter>

    <x-bs::list class="my-3">
        @include('storage_locations.shared.storage_location_list_items', [
            'marginLevel' => 0,
            'storageLocations' => $storageLocations,
            'showChildren' => ValueHelper::getFromQueryOrDefault('sort') === StorageLocation::HIERARCHICAL,
        ])
    </x-bs::list>

    {{ $storageLocations->withQueryString()->links() }}
@endsection
