@extends('layouts.app')

@php
    use App\Models\StorageLocation;
    use Illuminate\Pagination\LengthAwarePaginator;

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

    <x-bs::list class="my-3">
        @include('storage_locations.shared.storage_location_list_items', [
            'marginLevel' => 0,
            'storageLocations' => $storageLocations,
        ])
    </x-bs::list>

    {{ $storageLocations->withQueryString()->links() }}
@endsection
