@extends('layouts.app')

@php
    /** @var ?\App\Models\Location $location */
@endphp

@section('title')
    @isset($location)
        {{ __('Edit :name', ['name' => $location->nameOrAddress]) }}
    @else
        {{ __('Create location') }}
    @endisset
@endsection

@section('breadcrumbs')
    @include('locations.shared.location_breadcrumbs')
@endsection

@section('headline-buttons')
    @isset($location)
        @can('forceDelete', $location)
            <x-form.delete-modal :id="$location->id"
                                 :name="$location->name"
                                 :route="route('locations.destroy', $location)"/>
        @endcan
    @endisset
@endsection

@section('content')
    <x-bs::form method="{{ isset($location) ? 'PUT' : 'POST' }}"
                action="{{ isset($location) ? route('locations.update', $location) : route('locations.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
                                  :value="$location->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="website_url" type="text"
                                  :value="$location->website_url ?? null"><i class="fa fa-fw fa-display"></i> {{ __('Website') }}</x-bs::form.field>
                @include('_shared.address_fields_form')
            </div>
        </div>

        <x-button.group-save :show-create="!isset($location)"
                             :index-route="route('locations.index')"/>
    </x-bs::form>

    <x-text.timestamp :model="$location ?? null"/>
@endsection
