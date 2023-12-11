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
    <x-bs::breadcrumb.item href="{{ route('user-roles.index') }}">{{ __('User roles') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    @isset($userRole)
        <div class="mb-3">
            <x-bs::badge variant="primary">{{ formatTransChoice(':count users', $userRole->users()->count()) }}</x-bs::badge>
        </div>
    @endisset

    <x-bs::form method="{{ isset($userRole) ? 'PUT' : 'POST' }}"
                action="{{ isset($userRole) ? route('user-roles.update', $userRole) : route('user-roles.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
                                  :value="$userRole->name ?? null">{{ __('Name') }}</x-bs::form.field>
            </div>
        </div>
        <x-bs::form.field id="abilities" name="abilities[]" type="switch"
                          :options="\App\Options\Ability::toOptions()"
                          :value="$userRole->abilities ?? []"
                          check-container-class="cols-lg-2 cols-xl-3 cols-xxl-4">{{ __('Abilities') }}</x-bs::form.field>

        <x-bs::button.group>
            <x-button.save>
                @isset($userRole){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('user-roles.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$userRole ?? null" />
@endsection
