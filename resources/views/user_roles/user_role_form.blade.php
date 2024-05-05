@extends('layouts.app')

@php
    /** @var ?\App\Models\UserRole $userRole */
@endphp

@section('title')
    @isset($userRole)
        {{ __('Edit :name', ['name' => $userRole->name]) }}
    @else
        {{ __('Create user role') }}
    @endisset
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\UserRole::class)
        <x-bs::breadcrumb.item href="{{ route('user-roles.index') }}">{{ __('User roles') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('User roles') }}</x-bs::breadcrumb.item>
    @endcan
    @isset($userRole)
        @can('view', $userRole)
            <x-bs::breadcrumb.item href="{{ route('user-roles.show', $userRole) }}">{{ $userRole->name }}</x-bs::breadcrumb.item>
        @else
            <x-bs::breadcrumb.item>{{ $userRole->name }}</x-bs::breadcrumb.item>
        @endcan
    @endisset
@endsection

@section('content')
    @isset($userRole)
        @include('user_roles.shared.user_role_badge')
    @endisset

    <x-bs::form method="{{ isset($userRole) ? 'PUT' : 'POST' }}"
                action="{{ isset($userRole) ? route('user-roles.update', $userRole) : route('user-roles.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
                                  :value="$userRole->name ?? null">{{ __('Name') }}</x-bs::form.field>
            </div>
        </div>

        <h2>{{ __('Abilities') }}</h2>
        <div class="cols-lg-2 cols-xxl-3 mb-3">
            @include('user_roles.ability_group', [
                'abilityGroups' => \App\Options\AbilityGroup::casesAtRootLevel(),
                'editable' => true,
                'headlineLevel' => 3,
            ])
        </div>

        <x-bs::button.group>
            <x-button.save>
                @isset($userRole)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('user-roles.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$userRole ?? null"/>
@endsection
