@extends('layouts.app')

@php
    use App\Enums\AbilityGroup;
    use App\Models\User;

    /** @var User $user */
@endphp

@section('title')
    {{ __('Abilities by :name', [
        'name' => $user->name,
    ]) }}
@endsection

@section('breadcrumbs')
    @include('users.shared.user_breadcrumbs')
    <x-bs::breadcrumb.item>{{ __('Abilities') }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('edit', $user)
        <x-bs::button.link href="{{ route('users.edit', $user) }}"><i class="fa fa-fw fa-edit"></i> {{ __('Edit') }}</x-bs::button.link>
    @endif
@endsection

@section('content')
    @include('users.shared.user_profile_data')

    <section>
        <h2 class="mt-3"><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</h2>
        <div class="cols-lg-2 cols-xxl-3 mb-3">
            @include('user_roles.ability_group', [
                'selectedAbilities' => $user->getAbilitiesAsStrings()->toArray(),
                'abilityGroups' => AbilityGroup::casesAtRootLevel(),
                'editable' => false,
                'headlineLevel' => 3,
            ])
        </div>
    </section>
@endsection
