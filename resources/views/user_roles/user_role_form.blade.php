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
    <x-nav.breadcrumb href="{{ route('user-roles.index') }}">{{ __('User roles') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    @isset($userRole)
        <div class="mb-3">
            <span class="badge ba bg-primary">{{ formatTransChoice(':count users', $userRole->users()->count()) }}</span>
        </div>
    @endisset

    <x-form method="{{ isset($userRole) ? 'PUT' : 'POST' }}"
            action="{{ isset($userRole) ? route('user-roles.update', $userRole) : route('user-roles.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$userRole->name ?? null" />
                </x-form.row>
            </div>
        </div>
        <x-form.row>
            <x-form.label for="abilities">{{ __('Abilities') }}</x-form.label>
            <div class="cols-lg-2 cols-xl-3 cols-xxl-4">
                <x-form.input for="abilities" name="abilities[]" type="checkbox"
                              :options="\App\Options\Ability::keysWithNames()"
                              :value="$userRole->abilities ?? []" />
            </div>
        </x-form.row>

        <x-button.group>
            <x-button.save>
                @isset($userRole){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('user-roles.index') }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$userRole ?? null" />
@endsection
