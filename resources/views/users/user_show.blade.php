@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
@endphp

@section('title')
    {{ $user->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('users.index') }}">{{ __('Users') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>{{ $user->name }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    <x-button.edit href="{{ route('users.edit', $user) }}"/>
@endsection

@section('content')
    @include('users.shared.user_profile_data')

    @include('users.shared.user_profile_responsibilities')

    <x-text.timestamp :model="$user ?? null"/>
@endsection
