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
            <x-bs::breadcrumb.item href="{{ route('storage-locations.show', $parentStorageLocation) }}">{{ $parentStorageLocation->name }}</x-bs::breadcrumb.item>
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

        @livewire('storage-locations.select-storage-location', [
            'storageLocation' => $storageLocation ?? null,
            'selectedStorageLocation' => $storageLocation->parentStorageLocation ?? null,
        ])

        <x-bs::button.group>
            <x-button.save>
                @isset($storageLocation)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('storage-locations.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$storageLocation ?? null"/>
@endsection
