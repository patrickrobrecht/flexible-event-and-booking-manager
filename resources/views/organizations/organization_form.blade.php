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
    @include('organizations.shared.organization_breadcrumbs')
@endsection

@section('content')
    <x-bs::form method="{{ isset($organization) ? 'PUT' : 'POST' }}"
                action="{{ isset($organization) ? route('organizations.update', $organization) : route('organizations.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
                                  :value="$organization->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="slug" type="text"
                                  :value="$organization->slug ?? null">
                    {{ __('Slug') }}
                    <x-slot:hint>
                        {!! __('This field defines the path in the URL, such as :url. If you leave it empty, is auto-generated for you.', [
                            'url' => isset($organization->slug)
                                ? sprintf('<a href="%s" target="_blank">%s</a>', route('organizations.show', $organization), route('organizations.show', $organization, false))
                                : '<strong>' . route('organizations.show', Str::of(__('Name of the organization'))->snake('-')) . '</strong>'
                        ]) !!}
                    </x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="status" type="select"
                                  :options="\App\Options\ActiveStatus::toOptions()"
                                  :value="$organization->status->value ?? null"><i class="fa fa-fw fa-circle-question"></i> {{ __('Status') }}</x-bs::form.field>
                <x-bs::form.field name="register_entry" type="text"
                                  :value="$organization->register_entry ?? null"><i class="fa fa-fw fa-scale-balanced"></i> {{ __('Register entry') }}</x-bs::form.field>
                <x-bs::form.field name="website_url" type="url"
                                  :value="$organization->website_url ?? null"><i class="fa fa-fw fa-display"></i> {{ __('Website') }}</x-bs::form.field>
                <x-bs::form.field name="location_id" type="select"
                                  :options="$locations->pluck('nameOrAddress', 'id')"
                                  :value="$organization->location->id ?? null"><i class="fa fa-fw fa-location-pin"></i> {{ __('Location') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6">
                <section class="my-3">
                    <h2><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</h2>
                    @livewire('users.search-users', [
                        'selectedUsers' => $organization->responsibleUsers ?? \Illuminate\Database\Eloquent\Collection::empty(),
                    ])
                </section>
            </div>
        </div>

        <x-bs::button.group>
            <x-button.save>
                @isset($organization)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('organizations.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$organization ?? null"/>
@endsection
