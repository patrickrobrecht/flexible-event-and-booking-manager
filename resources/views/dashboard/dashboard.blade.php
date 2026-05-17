@extends('layouts.app')

@php
    use App\Models\Booking;
    use App\Models\Event;
    use App\Models\User;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Support\Facades\Auth;

    /** @var Collection<int, Event> $events */
    /** @var Collection<int, Booking>|null $bookings */
    /** @var Collection<int, int>|null $allDocumentsByStatus */

    $showBookingsColumn = $bookings !== null;
    $showDocumentsColumn = $allDocumentsByStatus !== null;

    $colClass = 'col-12 col-md-6';
    if ($showBookingsColumn && $showDocumentsColumn) {
        $colClass = 'col-12 col-xl-6 col-xxl-4';
    }
@endphp

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    @include('account.shared.unverified_email')

    <div class="row">
        <div id="next-events" class="{{ $colClass }}">
            <h2><i class="fa fa-fw fa-calendar-days"></i> {{ __('Next events') }}</h2>
            @include('events.shared.event_list', [
                'events' => $events,
                'showVisibility' => false,
                'noEventsMessage' => __('There are no public future events.'),
            ])
        </div>
        @if($showBookingsColumn)
            <div id="my-bookings" class="{{ $colClass }} mt-4 mt-xl-0">
                <h2><i class="fa fa-fw fa-file-contract"></i> <a href="{{ route('account.bookings') }}">{{ __('My bookings') }}</a></h2>
                @if($bookings->isEmpty())
                    <x-bs::alert variant="info">
                        {{ __('You do not have any bookings yet.') }}
                    </x-bs::alert>
                @else
                    @include('bookings.shared.booking_list')
                @endif
            </div>
        @endif
        @if($showDocumentsColumn)
            <div id="documents" class="{{ $colClass }} mt-4 mt-xl-0">
                <h2><i class="fa fa-fw fa-file"></i> <a href="{{ route('documents.index') }}">{{ __('Documents') }}</a></h2>
                @include('dashboard.documents_by_status', [
                    'documentsByStatus' => $allDocumentsByStatus,
                ])
            </div>
        @endif
    </div>
@endsection
