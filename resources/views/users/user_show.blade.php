@extends('layouts.app')

@php
    use App\Enums\AbilityGroup;
    use App\Models\User;

    /** @var User $user */
@endphp

@section('title')
    {{ $user->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', User::class)
        <x-bs::breadcrumb.item href="{{ route('users.index') }}">{{ __('Users') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Users') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ $user->name }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('viewAbilities', $user)
        <x-bs::button.link variant="secondary" href="{{ route('users.abilities', $user) }}"><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</x-bs::button.link>
    @endcan
    @can('update', $user)
        <x-button.edit href="{{ route('users.edit', $user) }}"/>
    @endcan
    @include('users.shared.user_delete_button')
@endsection

@section('content')
    @include('users.shared.user_profile_data')

    <div class="row">
        @include('users.shared.user_profile_responsibilities')
        @include('users.shared.user_profile_bookings', [
            'allBookingsLink' => route('users.bookings', $user),
        ])
        @include('users.shared.user_profile_documents', [
            'allDocumentsLink' => route('users.documents', $user),
            'documentsByStatus' => $user->documents_by_status,
        ])
    </div>

    <x-text.timestamp :model="$user ?? null"/>
@endsection
