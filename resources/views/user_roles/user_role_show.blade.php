@extends('layouts.app')

@php
    /** @var \App\Models\UserRole $userRole */
@endphp

@section('title')
    {{ $userRole->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\Organization::class)
        <x-bs::breadcrumb.item href="{{ route('user-roles.index') }}">{{ __('User roles') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('User roles') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ $userRole->name }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $userRole)
        <x-button.edit href="{{ route('user-roles.edit', $userRole) }}"/>
    @endcan
@endsection

@section('content')
    @include('user_roles.shared.user_role_badge_count')

    <h2>{{ __('Abilities') }}</h2>
    <div class="cols-lg-2 cols-xxl-3 mb-3">
        @include('user_roles.ability_group', [
            'selectedAbilities' => $userRole->abilities,
            'abilityGroups' => \App\Options\AbilityGroup::casesAtRootLevel(),
            'editable' => false,
            'headlineLevel' => 3,
        ])
    </div>

    <x-text.timestamp :model="$userRole ?? null"/>
@endsection
