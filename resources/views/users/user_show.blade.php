@extends('layouts.app')

@php
    /** @var ?\App\Models\User $user */
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
    <div class="my-1">
        <span class="text-nowrap me-3"><x-badge.active-status :active="$user->status"/></span>
        <span class="text-nowrap me-3"><i class="fa fa-fw fa-sign-in-alt"></i> {{ __('Last login') }}
            {{ $user->last_login_at ? formatDateTime($user->last_login_at) : __('never') }}</span>
        @if($user->userRoles->isNotEmpty())
            <span class="me-3">
                <i class="fa fa-fw fa-user-group" title="{{ __('User roles') }}"></i>
                @foreach($user->userRoles->sortBy('name') as $userRole)
                        <x-bs::badge variant="primary">{{ $userRole->name }}</x-bs::badge>
                @endforeach
            </span>
        @endif
    </div>

    <div class="mb-3">
        @isset($user->date_of_birth)
            <span class="text-nowrap me-3"><i class="fa fa-fw fa-cake-candles" title="{{ __('Date of birth') }}"></i> {{ formatDate($user->date_of_birth) }}</span>
        @endisset
        @isset($user->phone)
            <span class="text-nowrap me-3"><i class="fa fa-fw fa-phone" title="{{ __('Phone number') }}"></i> {{ $user->phone }}</span>
        @endisset
        <span class="text-nowrap me-3"><i class="fa fa-fw fa-at" title="{{ __('E-mail') }}"></i> {{ $user->email }}
            @isset($user->email_verified_at)
                <x-bs::badge variant="success">{{ __('verified') }}</x-bs::badge>
            @else
                <x-bs::badge variant="danger">{{ __('not verified') }}</x-bs::badge>
            @endisset
        </span>
        @if(count($user->addressBlock) > 0)
            <span class="text-nowrap me-3"><i class="fa fa-fw fa-location-pin" title="{{ __('Address') }}"></i> {{ implode(', ', $user->addressBlock) }}</span>
        @endif
    </div>

    <div class="row">
        <div id="responsibilities" class="col-12 col-xl-6 col-xxl-4">
            <h2>{{ __('Responsibilities') }}</h2>
            @if(
                $user->responsibleForEvents->isEmpty()
                && $user->responsibleForEventSeries->isEmpty()
                && $user->responsibleForOrganizations->isEmpty()
            )
                <x-bs::alert class="danger">{{ __(':name has not been assigned any responsibilities.', [
                    'name' => $user->first_name,
                ]) }}</x-bs::alert>
            @endif
            @if($user->responsibleForOrganizations->isNotEmpty())
                <div class="mb-3">
                    <h3>{{ __('Organizations') }}</h3>
                    @include('organizations.shared.organization_list', [
                        'organizations' => $user->responsibleForOrganizations,
                    ])
                </div>
            @endif
            @if($user->responsibleForEventSeries->isNotEmpty())
                <div class="mb-3">
                    <h3>{{ __('Event series') }}</h3>
                    @include('event_series.shared.event_series_list', [
                        'eventSeries' => $user->responsibleForEventSeries,
                    ])
                </div>
            @endif
            @if($user->responsibleForEvents->isNotEmpty())
                <div class="mb-3">
                    <h3>{{ __('Events') }}</h3>
                    @include('events.shared.event_list', [
                        'events' => $user->responsibleForEvents,
                    ])
                </div>
            @endif
        </div>
        <div id="bookings" class="col-12 col-xl-6 col-xxl-4">
            <h2>{{ __('Bookings') }}</h2>
            @if($user->bookings->count() === 0)
                <x-bs::alert class="danger">{{ __(':name does not have any bookings yet.', [
                    'name' => $user->first_name,
                ]) }}</x-bs::alert>
            @endif
            @include('bookings.shared.booking_list', [
                'bookings' => $user->bookings,
            ])
        </div>
        <div id="documents" class="col-12 col-xl-6 col-xxl-4">
            <h2>{{ __('Documents') }}</h2>
            @if($user->documents->count() === 0)
                <x-bs::alert class="danger">{{ __(':name has not uploaded any documents yet.', [
                    'name' => $user->first_name,
                ]) }}</x-bs::alert>
            @endif
            @include('documents.shared.document_list', [
                'documents' => $user->documents,
            ])
        </div>
    </div>

    <x-text.timestamp :model="$user ?? null" />
@endsection
