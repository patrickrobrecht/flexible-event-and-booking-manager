@extends('layouts.app')

@php
    /** @var ?\App\Models\Organization $organization */
@endphp

@section('title')
    @isset($organization)
        {{ __('Edit :name', ['name' => $organization->name]) }}
    @else
        {{ __('Create organization') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('organizations.index') }}">{{ __('Organizations') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($organization) ? 'PUT' : 'POST' }}"
            action="{{ isset($organization) ? route('organizations.update', $organization) : route('organizations.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$organization->name ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="status">{{ __('Status') }}</x-form.label>
                    <x-form.select name="status"
                                   :options="\App\Options\ActiveStatus::keysWithNames()"
                                   :value="$organization->status->value ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="register_entry">{{ __('Register entry') }}</x-form.label>
                    <x-form.input name="register_entry" type="text"
                                  :value="$organization->register_entry ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="representatives">{{ __('Representatives') }}</x-form.label>
                    <x-form.input name="representatives" type="text"
                                  :value="$organization->representatives ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="website_url">{{ __('Website') }}</x-form.label>
                    <x-form.input name="website_url" type="text"
                                  :value="$event->website_url ?? null"/>
                </x-form.row>
            </div>
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="location_id">{{ __('Location') }}</x-form.label>
                    <x-form.select name="location_id"
                                   :options="$locations->pluck('nameOrAddress', 'id')"
                                   :value="$organization->location->id ?? null"/>
                </x-form.row>
            </div>
        </div>

        <x-button.group>
            <x-button.save>
                @isset($organization)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('organizations.index') }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$organization ?? null"/>
@endsection
