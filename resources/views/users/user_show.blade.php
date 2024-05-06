@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
@endphp

@section('title')
    {{ $user->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\User::class)
        <x-bs::breadcrumb.item href="{{ route('users.index') }}">{{ __('Users') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Users') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ $user->name }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $user)
        <x-button.edit href="{{ route('users.edit', $user) }}"/>
    @endcan
@endsection

@section('content')
    @include('users.shared.user_profile_data')

    @include('users.shared.user_profile_responsibilities')

    @can('viewAny', \App\Models\UserRole::class)
        <section id="abilities" class="mt-3">
            <h2>{{ __('Abilities') }}</h2>
            <div class="cols-lg-2 cols-xxl-3 mb-3">
                @include('user_roles.ability_group', [
                    'selectedAbilities' => $user->getAbilitiesAsStrings()->toArray(),
                    'abilityGroups' => \App\Options\AbilityGroup::casesAtRootLevel(),
                    'editable' => false,
                    'headlineLevel' => 3,
                ])
            </div>
        </section>
    @endcan

    <x-text.timestamp :model="$user ?? null"/>
@endsection
