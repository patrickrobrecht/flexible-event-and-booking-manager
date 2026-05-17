@extends('layouts.app')

@php
    use App\Models\Booking;
    use App\Enums\ApprovalStatus;
    use App\Models\Event;
    use App\Models\EventSeries;
    use App\Models\Organization;
    use App\Models\User;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Support\Collection as SupportCollection;
    use Illuminate\Support\Facades\Auth;

    /** @var Collection<int, Event> $events */
    /** @var Collection<int, Booking>|null $bookings */
    /** @var SupportCollection<value-of<ApprovalStatus>, int>|null $allDocumentsByStatus */
    /** @var SupportCollection<value-of<ApprovalStatus>, int>|null $myDocumentsByStatus */
    /** @var Collection<int, Event>|null $myEventsWithoutDocuments */
    /** @var Collection<int, EventSeries>|null $myEventSeriesWithoutDocuments */
    /** @var Collection<int, Organization>|null $myOrganizationsWithoutDocuments */

    $showBookingsColumn = $bookings !== null;
    $showMissingDocuments = array_any(
        [$myEventsWithoutDocuments, $myEventSeriesWithoutDocuments, $myOrganizationsWithoutDocuments],
        fn ($collection) => $collection !== null && $collection->isNotEmpty()
    );
    $showDocumentsColumn = $allDocumentsByStatus !== null || $myDocumentsByStatus !== null || $showMissingDocuments;

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
                @if($allDocumentsByStatus !== null)
                    @include('documents.shared.documents_by_status', [
                        'documentsByStatus' => $allDocumentsByStatus,
                        'route' => route('documents.index'),
                    ])
                @endif

                @if($myDocumentsByStatus !== null)
                    <h3 class="mt-4"><a href="{{ route('account.documents') }}">{{ __('My documents') }}</a></h3>
                    @include('documents.shared.documents_by_status', [
                        'documentsByStatus' => $myDocumentsByStatus,
                        'route' => route('account.documents'),
                    ])
                @endif

                @if($showMissingDocuments)
                    <h3 class="mt-4">{{ __('Missing documents') }}</h3>
                    <x-bs::list>
                        @if($myEventsWithoutDocuments !== null && $myEventsWithoutDocuments->isNotEmpty())
                            <x-bs::list.item class="fw-bold">{{ __('Events') }}</x-bs::list.item>
                            @foreach($myEventsWithoutDocuments as $event)
                                <x-bs::list.item>
                                    <a href="{{ route('events.show', $event) }}">{{ $event->name }}</a>
                                </x-bs::list.item>
                            @endforeach
                        @endif
                        @if($myEventSeriesWithoutDocuments !== null && $myEventSeriesWithoutDocuments->isNotEmpty())
                            <x-bs::list.item class="fw-bold">{{ __('Event series') }}</x-bs::list.item>
                            @foreach($myEventSeriesWithoutDocuments as $eventSeries)
                                <x-bs::list.item>
                                    <a href="{{ route('event-series.show', $eventSeries) }}">{{ $eventSeries->name }}</a>
                                </x-bs::list.item>
                            @endforeach
                        @endif
                        @if($myOrganizationsWithoutDocuments !== null && $myOrganizationsWithoutDocuments->isNotEmpty())
                            <x-bs::list.item class="fw-bold">{{ __('Organizations') }}</x-bs::list.item>
                            @foreach($myOrganizationsWithoutDocuments as $organization)
                                <x-bs::list.item>
                                    <a href="{{ route('organizations.show', $organization) }}">{{ $organization->name }}</a>
                                </x-bs::list.item>
                            @endforeach
                        @endif
                    </x-bs::list>
                @endif
            </div>
        @endif
    </div>
@endsection
