@extends('layouts.app')

@php
    use App\Models\Booking;
    use App\Models\User;
    use Illuminate\Pagination\LengthAwarePaginator;

    /** @var LengthAwarePaginator<int, Booking> $bookings */
@endphp

@section('title')
    {{ __('My bookings') }}
@endsection

@section('breadcrumbs')
    @can('viewAccount', User::class)
        <x-bs::breadcrumb.item href="{{ route('account.show') }}">{{ __('My account') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ __('My bookings') }}</x-bs::breadcrumb.item>
@endsection

@section('content')
    @if($bookings->isEmpty())
        <x-bs::alert variant="info">
            {{ __('You do not have any bookings yet.') }}
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
