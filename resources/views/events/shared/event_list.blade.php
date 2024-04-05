@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events */
    /** @var ?string $noEventsMessage */
    $showVisibility = $showVisibility ?? true;
@endphp

@if($events->count() === 0)
    @isset($noEventsMessage)
        <x-bs::alert variant="danger">{{ $noEventsMessage }}</x-bs::alert>
    @endisset
@else
    <div class="list-group">
        @foreach($events as $event)
            @can('view', $event)
                <x-bs::list.item>
                    <a href="{{ route('events.show', $event->slug) }}" class="fw-bold">{{ $event->name }}</a>
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
                    @isset($event->description)
                        <div class="text-muted">{{ $event->description }}</div>
                    @endisset
                    @canany(['update', 'viewGroups'], $event)
                        <x-bs::button.group class="mt-3">
                            @can('update', $event)
                                <x-button.edit href="{{ route('events.edit', $event) }}"/>
                            @endcan
                            @can('viewGroups', $event)
                                <x-bs::button.link href="{{ route('groups.index', $event) }}" variant="secondary">
                                    <i class="fa fa-fw fa-user-group"></i> {{ __('Groups') }} <x-bs::badge variant="danger">{{ formatInt($event->groups_count) }}</x-bs::badge>
                                </x-bs::button.link>
                            @endcan
                        </x-bs::button.group>
                    @endcanany
                </x-bs::list.item>
            @endcan
        @endforeach
    </div>
@endif
