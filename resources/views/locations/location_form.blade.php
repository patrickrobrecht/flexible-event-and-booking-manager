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
    <x-bs::breadcrumb.item href="{{ route('locations.index') }}">{{ __('Locations') }}</x-bs::breadcrumb.item>
    @isset($location)
        <x-bs::breadcrumb.item>{{ $location->nameOrAddress }}</x-bs::breadcrumb.item>
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

        <x-bs::button.group>
            <x-button.save>
                @isset($location){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('locations.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$location ?? null"/>
@endsection
