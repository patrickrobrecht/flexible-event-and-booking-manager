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
    <x-nav.breadcrumb href="{{ route('locations.index') }}">{{ __('Locations') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($location) ? 'PUT' : 'POST' }}"
            action="{{ isset($location) ? route('locations.update', $location) : route('locations.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$location->name ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="website_url">{{ __('Website') }}</x-form.label>
                    <x-form.input name="website_url" type="text"
                                  :value="$event->website_url ?? null"/>
                </x-form.row>
                @include('_shared.address_fields_form')
            </div>
        </div>

        <x-button.group>
            <x-button.save>
                @isset($location){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('locations.index') }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$location ?? null" />
@endsection
