@extends('layouts.app')

@php
    use App\Models\User;
    use Illuminate\Support\Facades\Auth;

    /** @var User $user */
@endphp

@section('title')
    {{ __('My account') }}: {{ $user->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>{{ __('My account') }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('viewAccountAbilities', User::class)
        <x-bs::button.link variant="secondary" href="{{ route('account.abilities') }}"><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</x-bs::button.link>
    @endif
    @can('editAccount', User::class)
        <x-bs::button.link href="{{ route('account.edit') }}"><i class="fa fa-fw fa-user-pen"></i> {{ __('Edit my account') }}</x-bs::button.link>
    @endif
@endsection

@section('content')
    @include('users.shared.user_profile_data', [
        'user' => $user,
    ])
    @include('account.shared.unverified_email')

    <div class="row">
        @include('users.shared.user_profile_responsibilities')
        @include('users.shared.user_profile_bookings', [
            'allBookingsLink' => route('account.bookings'),
        ])
        @include('users.shared.user_profile_documents', [
            'allDocumentsLink' => route('account.documents'),
            'documentsByStatus' => $user->documents_by_status,
        ])
    </div>
@endsection
