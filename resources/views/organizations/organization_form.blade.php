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

@section('headline-buttons')
    @isset($organization)
        @can('forceDelete', $organization)
            <x-form.delete-modal :id="$organization->id"
                                 :name="$organization->name"
                                 :route="route('organizations.destroy', $organization)"/>
        @endcan
    @endisset
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
                                  :options="\App\Enums\ActiveStatus::toOptions()"
                                  :value="$organization->status->value ?? null"><i class="fa fa-fw fa-circle-question"></i> {{ __('Status') }}</x-bs::form.field>
                <x-bs::form.field name="register_entry" type="text"
                                  :value="$organization->register_entry ?? null"><i class="fa fa-fw fa-scale-balanced"></i> {{ __('Register entry') }}</x-bs::form.field>
                <x-bs::form.field name="website_url" type="url"
                                  :value="$organization->website_url ?? null"><i class="fa fa-fw fa-display"></i> {{ __('Website') }}</x-bs::form.field>
                <x-bs::form.field name="phone" type="tel"
                                  :value="$organization->phone ?? null"><i class="fa fa-fw fa-phone"></i> {{ __('Phone number') }}</x-bs::form.field>
                <x-bs::form.field name="email" type="email"
                                  :value="$organization->email ?? null"><i class="fa fa-fw fa-at"></i> {{ __('E-mail') }}</x-bs::form.field>
                <x-bs::form.field name="location_id" type="select" :options="$locations->pluck('nameOrAddress', 'id')"
                                  :value="$organization->location->id ?? null"><i class="fa fa-fw fa-location-pin"></i> {{ __('Location') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6">
                <section class="my-3">
                    <h2><i class="fa fa-fw fa-credit-card"></i> {{ __('Bank account details') }}</h2>
                    <x-bs::form.field name="bank_account_holder" type="text" placeholder="{{ $organization->name ?? null }}"
                                      :value="$organization->bank_account_holder ?? null">{{ __('Account holder') }}</x-bs::form.field>
                    <x-bs::form.field name="iban" type="text"
                                      :value="$organization->iban ?? null"><abbr title="{{ __('International Bank Account Number') }}">IBAN</abbr></x-bs::form.field>
                    <x-bs::form.field name="bank_name" type="text"
                                      :value="$organization->bank_name ?? null">{{ __('Name of the bank') }}</x-bs::form.field>
                </section>
                <section class="my-3">
                    <h2><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</h2>
                    @livewire('users.search-users', [
                        'selectedUsers' => $organization->responsibleUsers ?? \Illuminate\Database\Eloquent\Collection::empty(),
                    ])
                </section>
            </div>
        </div>

        <x-button.group-save :show-create="!isset($organization)"
                             :index-route="route('organizations.index')"/>
    </x-bs::form>

    <x-text.timestamp :model="$organization ?? null"/>
@endsection
