@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
    $user = \Illuminate\Support\Facades\Auth::user()->loadProfileData();
@endphp

@section('title')
    {{ __('My account') }}: {{ $user->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>{{ __('My account') }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('viewAbilities', \App\Models\User::class)
        <x-bs::button.link variant="secondary" href="{{ route('account.show.abilities') }}"><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</x-bs::button.link>
    @endif
    @can('editAccount', \App\Models\User::class)
        <x-bs::button.link href="{{ route('account.edit') }}"><i class="fa fa-fw fa-user-pen"></i> {{ __('Edit my account') }}</x-bs::button.link>
    @endif
@endsection

@section('content')
    @include('users.shared.user_profile_data', [
        'user' => $user,
    ])
    @include('account.shared.unverified_email')

    @include('users.shared.user_profile_responsibilities', [
        'user' => $user,
    ])
@endsection
