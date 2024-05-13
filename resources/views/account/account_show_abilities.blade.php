@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

@section('title')
    {{ __('My account') }}: {{ $user->name }}
@endsection

@section('breadcrumbs')
    @can('viewAccount', \App\Models\User::class)
        <x-bs::breadcrumb.item href="{{ route('account.show') }}">{{ __('My account') }}</x-bs::breadcrumb.item>
    @endcan
@endsection

@section('headline-buttons')
    @can('editAccount', \App\Models\User::class)
        <x-bs::button.link href="{{ route('account.edit') }}"><i class="fa fa-fw fa-user-pen"></i> {{ __('Edit my account') }}</x-bs::button.link>
    @endif
@endsection

@section('content')
    @include('users.shared.user_profile_data', [
        'user' => $user,
    ])

    <section>
        <h2 class="mt-3"><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</h2>
        <div class="cols-lg-2 cols-xxl-3 mb-3">
            @include('user_roles.ability_group', [
                'selectedAbilities' => $user->getAbilitiesAsStrings()->toArray(),
                'abilityGroups' => \App\Options\AbilityGroup::casesAtRootLevel(),
                'editable' => false,
                'headlineLevel' => 3,
            ])
        </div>
    </section>
@endsection
