@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
    $user = \Illuminate\Support\Facades\Auth::user()->loadProfileData();
@endphp

@section('title')
    {{ __('My account') }}: {{ $user->name }}
@endsection

@section('headline-buttons')
    @can('editAccount', \App\Models\User::class)
        <x-bs::button.link href="{{ route('account.edit') }}">{{ __('Edit my account') }}</x-bs::button.link>
    @endif
@endsection

@section('content')
    @include('users.shared.user_profile', [
        'user' => $user,
    ])
@endsection
