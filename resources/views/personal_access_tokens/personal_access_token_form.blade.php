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
    <x-bs::form method="{{ isset($token) ? 'PUT' : 'POST' }}"
                action="{{ isset($token) ? route('personal-access-tokens.update', $token) : route('personal-access-tokens.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
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
        <x-bs::form.field id="abilities" name="abilities[]" type="switch"
                          :options="\Portavice\Bladestrap\Support\Options::fromEnum(\App\Options\Ability::apiCases(), 'getTranslatedName')"
                          :value="$token->abilities ?? []"
                          check-container-class="cols-lg-2 cols-xl-3 cols-xxl-4"><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</x-bs::form.field>

        <x-bs::button.group>
            <x-button.save>
                @isset($token){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('personal-access-tokens.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$token ?? null" />
@endsection
