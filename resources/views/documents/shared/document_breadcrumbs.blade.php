@php
    use App\Enums\FileType;
    use App\Models\Event;
    use App\Models\EventSeries;
    use App\Models\Location;
    use App\Models\Organization;

    /** @var Event|EventSeries|Location|Organization $reference */
@endphp
@if($reference::class === Event::class)
    @include('events.shared.event_breadcrumbs', [
        'event' => $reference,
    ])
@elseif($reference::class === EventSeries::class)
    @include('event_series.shared.event_series_breadcrumbs', [
        'eventSeries' => $reference,
    ])
@elseif($reference::class === Location::class)
    @include('locations.shared.location_breadcrumbs', [
        'location' => $reference,
    ])
@elseif($reference::class === Organization::class)
    @include('organizations.shared.organization_breadcrumbs', [
        'organization' => $reference,
    ])
@endif
@isset($document)
    @if($document->file_type === FileType::Image)
        <x-bs::breadcrumb.item href="{{ $document->reference->getRouteForGallery() }}">{{ __('Image gallery') }}</x-bs::breadcrumb.item>
    @endif
@endisset
