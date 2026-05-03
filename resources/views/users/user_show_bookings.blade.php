@extends('layouts.app')

@php
    use App\Models\Booking;
    use App\Models\User;
    use Illuminate\Pagination\LengthAwarePaginator;

    /** @var User $user */
    /** @var LengthAwarePaginator<int, Booking> $bookings */
@endphp

@section('title')
    {{ __('Bookings by :name', [
        'name' => $user->name,
    ]) }}
@endsection

@section('breadcrumbs')
    @can('viewAny', User::class)
        <x-bs::breadcrumb.item href="{{ route('users.index') }}">{{ __('Users') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Users') }}</x-bs::breadcrumb.item>
    @endcan
    @can('view', $user)
        <x-bs::breadcrumb.item href="{{ route('users.show', $user) }}">{{ $user->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $user->name }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ __('Bookings') }}</x-bs::breadcrumb.item>
@endsection

@section('content')
    @if($bookings->isEmpty())
        <x-bs::alert variant="info">
            {{ __(':name does not have any bookings yet.', [
                'name' => $user->name,
            ]) }}
        </x-bs::alert>
    @else
        <div class="row my-3">
            @foreach($bookings as $booking)
                <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-3">
                    @include('bookings.shared.booking_card', [
                        'showEvent' => true,
                        'showGroups' => false,
                    ])
                </div>
            @endforeach
        </div>

        {{ $bookings->withQueryString()->links() }}
    @endif
@endsection
