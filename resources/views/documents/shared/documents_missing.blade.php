@php
    use App\Models\Event;
    use App\Models\EventSeries;
    use App\Models\Organization;
    use Illuminate\Database\Eloquent\Collection;

    /** @var Collection<int, Event>|null $eventsWithoutDocuments */
    /** @var Collection<int, EventSeries>|null $eventSeriesWithoutDocuments */
    /** @var Collection<int, Organization>|null $organizationsWithoutDocuments */

    $showMissingDocuments = array_any(
        [$eventsWithoutDocuments, $eventSeriesWithoutDocuments, $organizationsWithoutDocuments],
        fn ($collection) => $collection !== null && $collection->isNotEmpty()
    );
@endphp
@if($showMissingDocuments)
    <h3 class="mt-4">{{ __('Missing documents') }}</h3>
    <x-bs::list>
        @if($eventsWithoutDocuments !== null && $eventsWithoutDocuments->isNotEmpty())
            <x-bs::list.item class="fw-bold">{{ __('Events') }}</x-bs::list.item>
            @foreach($eventsWithoutDocuments as $event)
                <x-bs::list.item>
                    <a href="{{ route('events.show', $event) }}">{{ $event->name }}</a>
                </x-bs::list.item>
            @endforeach
        @endif
        @if($eventSeriesWithoutDocuments !== null && $eventSeriesWithoutDocuments->isNotEmpty())
            <x-bs::list.item class="fw-bold">{{ __('Event series') }}</x-bs::list.item>
            @foreach($eventSeriesWithoutDocuments as $eventSeries)
                <x-bs::list.item>
                    <a href="{{ route('event-series.show', $eventSeries) }}">{{ $eventSeries->name }}</a>
                </x-bs::list.item>
            @endforeach
        @endif
        @if($organizationsWithoutDocuments !== null && $organizationsWithoutDocuments->isNotEmpty())
            <x-bs::list.item class="fw-bold">{{ __('Organizations') }}</x-bs::list.item>
            @foreach($organizationsWithoutDocuments as $organization)
                <x-bs::list.item>
                    <a href="{{ route('organizations.show', $organization) }}">{{ $organization->name }}</a>
                </x-bs::list.item>
            @endforeach
        @endif
    </x-bs::list>
@endif
