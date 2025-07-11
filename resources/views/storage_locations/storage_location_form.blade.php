@extends('layouts.app')

@php
    /** @var ?\App\Models\StorageLocation $storageLocation */
@endphp

@section('title')
    @isset($storageLocation)
        {{ __('Edit :name', ['name' => $storageLocation->name]) }}
    @else
        {{ __('Create storage location') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('storage-locations.index') }}">{{ __('Storage locations') }}</x-bs::breadcrumb.item>
    @isset($storageLocation)
        @foreach($storageLocation->getAncestorsAndSelf() as $parentStorageLocation)
            <x-bs::breadcrumb.item href="{{ $parentStorageLocation->getRoute() }}">{{ $parentStorageLocation->name }}</x-bs::breadcrumb.item>
        @endforeach
    @endisset
@endsection

@section('headline-buttons')
    @isset($storageLocation)
        @can('forceDelete', $storageLocation)
            <x-form.delete-modal :id="$storageLocation->id"
                                 :name="$storageLocation->name"
                                 :route="route('storage-locations.destroy', $storageLocation)"/>
        @endcan
    @endisset
@endsection

@section('content')
    <x-bs::form method="{{ isset($storageLocation) ? 'PUT' : 'POST' }}"
                action="{{ isset($storageLocation) ? route('storage-locations.update', $storageLocation) : route('storage-locations.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text" :required="true"
                                  :value="$storageLocation->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="description" type="textarea"
                                  :value="$storageLocation->description ?? null">{{ __('Description') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6">
                <x-bs::form.field name="packaging_instructions" type="textarea"
                                  :value="$storageLocation->packaging_instructions ?? null">{{ __('Packaging instructions') }}</x-bs::form.field>
            </div>
        </div>

        @php
            $selectedStorageLocation = $storageLocation->parentStorageLocation ?? null;
            if (!isset($selectedStorageLocation)) {
                $parentStorageLocationIdFromQuery = \Portavice\Bladestrap\Support\ValueHelper::getFromQueryOrDefault('parent_storage_location_id');
                if (isset($parentStorageLocationIdFromQuery)) {
                    $selectedStorageLocation = \App\Models\StorageLocation::query()->find($parentStorageLocationIdFromQuery);
                }
            }
        @endphp
        @livewire('storage-locations.select-parent-storage-location', [
            'storageLocation' => $storageLocation ?? null,
            'selectedStorageLocation' => $selectedStorageLocation,
        ])

        <x-button.group-save :show-create="!isset($storageLocation)"
                             :index-route="route('storage-locations.index')"/>
    </x-bs::form>

    <x-text.timestamp :model="$storageLocation ?? null"/>
@endsection
