@php
    /** @var \App\Models\Document $document */
@endphp
@if($document->reference::class === \App\Models\Event::class)
    @include('events.shared.event_breadcrumbs', [
        'event' => $document->reference,
    ])
@elseif($document->reference::class === \App\Models\EventSeries::class)
    @include('event_series.shared.event_series_breadcrumbs', [
        'eventSeries' => $document->reference,
    ])
@elseif($document->reference::class === \App\Models\Location::class)
    @include('locations.shared.location_breadcrumbs', [
        'location' => $document->reference,
    ])
@elseif($document->reference::class === \App\Models\Organization::class)
    @include('organizations.shared.organization_breadcrumbs', [
        'organization' => $document->reference,
    ])
@endif
