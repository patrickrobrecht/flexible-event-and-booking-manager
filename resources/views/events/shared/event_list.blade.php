@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events */
    /** @var ?string $noEventsMessage */
@endphp

@if($events->count() === 0)
    @isset($noEventsMessage)
        <p class="alert alert-danger">
            {{ $noEventsMessage }}
        </p>
    @endisset
@else
    <ul class="list-group list-unstyled">
        @foreach($events as $event)
            <li>
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
                    <div class="text-muted">
                        {{ $event->description }}
                    </div>
                </a>
            </li>
        @endforeach
    </ul>
@endif
