@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events */
    /** @var ?string $noEventsMessage */
    $showVisibility = $showVisibility ?? true;
@endphp

@if($events->count() === 0)
    @isset($noEventsMessage)
        <p class="alert alert-danger">
            {{ $noEventsMessage }}
        </p>
    @endisset
@else
    <div class="list-group">
        @foreach($events as $event)
            @can('view', $event)
                <a href="{{ route('events.show', $event->slug) }}" class="list-group-item list-group-item-action">
                    <strong>{{ $event->name }}</strong>
                    <div>
                        <i class="fa fa-fw fa-clock"></i>
                        @include('events.shared.event_dates')
                    </div>
                    <div>
                        <i class="fa fa-fw fa-location-pin"></i>
                        {{ $event->location->nameOrAddress }}
                    </div>
                    @if($showVisibility)
                        <div>
                            <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                            <x-badge.visibility :visibility="$event->visibility"/>
                        </div>
                    @endif
                    <div class="text-muted">
                        {{ $event->description }}
                    </div>
                </a>
            @endcan
        @endforeach
    </div>
@endif
