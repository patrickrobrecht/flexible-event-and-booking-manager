@extends('layouts.app')

@php
    /** @var ?\App\Models\PersonalAccessToken $token */
@endphp

@section('title')
    @isset($token)
        {{ __('Edit :name', ['name' => $token->name]) }}
    @else
        {{ __('Create personal access token') }}
    @endisset
@endsection

@section('breadcrumbs')
    @can('viewOwn', \App\Models\PersonalAccessToken::class)
        <x-bs::breadcrumb.item href="{{ route('personal-access-tokens.index') }}">{{ __('Personal access tokens') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Personal access tokens') }}</x-bs::breadcrumb.item>
    @endcan
    @isset($token)
        <x-bs::breadcrumb.item>{{ $token->name }}</x-bs::breadcrumb.item>
    @endisset
@endsection

@section('content')
    @include('docs.docs-link')

    <x-bs::form method="{{ isset($token) ? 'PUT' : 'POST' }}"
                action="{{ isset($token) ? route('personal-access-tokens.update', $token) : route('personal-access-tokens.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text" maxlength="255" :required="true"
                                  :value="$token->name ?? null">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6">
                <x-bs::form.field name="expires_at" type="datetime-local"
                                  :value="isset($token->expires_at) ? $token->expires_at->format('Y-m-d\TH:i') : null">
                    <i class="fa fa-fw fa-calendar-times"></i> {{ __('Expires at') }}
                    @if(isset($token->expires_at) && $token->expires_at->isPast())
                        <x-bs::badge variant="danger">{{ __('expired') }}</x-bs::badge>
                    @endif
                    <x-slot:hint>{{ __('Last used') }}: {{ isset($token->last_used_at) ? formatDateTime($token->last_used_at) : __('never') }}</x-slot:hint>
                </x-bs::form.field>
            </div>
        </div>
        <div class="cols-lg-2 cols-xxl-3 mb-3">
            @include('user_roles.ability_group', [
                'selectableAbilities' => \App\Enums\Ability::apiCases(),
                'selectedAbilities' => $token->abilities ?? [],
                'abilityGroups' => \App\Enums\AbilityGroup::casesAtRootLevel(),
                'editable' => true,
                'headlineLevel' => 3,
            ])
        </div>

        <div class="d-flex flex-wrap gap-1">
            <x-bs::button>
                <i class="fa fa-fw fa-save"></i> {{ __('Save') }}
            </x-bs::button>
            <x-bs::button.link variant="danger" href="{{ route('personal-access-tokens.index') }}">
                <i class="fa fa-fw fa-window-close"></i> {{ __('Discard') }}
            </x-bs::button.link>
        </div>
    </x-bs::form>

    <x-text.timestamp :model="$token ?? null"/>
@endsection
