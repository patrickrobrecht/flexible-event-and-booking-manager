@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\EventSeries[] $eventSeries */
    /** @var ?string $noEventSeriesMessage */
    $showVisibility = $showVisibility ?? true;
    $showParentEventSeries = $showParentEventSeries ?? true;
@endphp

@if($eventSeries->count() === 0)
    @isset($noEventSeriesMessage)
        <x-bs::alert variant="danger">{{ $noEventSeriesMessage }}</x-bs::alert>
    @endisset
@else
    <div class="list-group">
        <x-bs::list container="div">
            @foreach($eventSeries as $eventSeriesItem)
                <x-bs::list.item>
                    <strong><a href="{{ route('event-series.show', $eventSeriesItem->slug) }}">{{ $eventSeriesItem->name }}</a></strong>
                    <div>
                        @if($showVisibility)
                            <x-badge.visibility :visibility="$eventSeriesItem->visibility"/>
                        @endif
                        @if($showParentEventSeries && isset($eventSeriesItem->parentEventSeries))
                            <x-bs::badge variant="primary">
                                <span><i class="fa fa-fw fa-calendar-week"></i> {{ __('Part of the event series') }}</span>
                                <a class="link-light" href="{{ route('event-series.show', $eventSeriesItem->parentEventSeries->slug) }}">{{ $eventSeriesItem->parentEventSeries->name }}</a>
                            </x-bs::badge>
                        @endif
                        <x-bs::badge>{{ formatTransChoice(':count events', $eventSeriesItem->events_count) }}</x-bs::badge>
                    </div>
                    @can('viewResponsibilities', $eventSeriesItem)
                        <div>
                            <i class="fa fa-fw fa-list-check" title="{{ __('Responsibilities') }}"></i>
                            @include('users.shared.responsible_user_span', [
                                'class' => null,
                                'users' => $eventSeriesItem->getResponsibleUsersVisibleForCurrentUser(),
                            ])
                        </div>
                    @endcan
                    @isset($eventSeriesItem->website_url)
                        <div>
                            <i class="fa fa-fw fa-display"></i>
                            <a href="{{ $event->website_url }}" target="_blank">{{ __('Website') }}</a>
                        </div>
                    @endisset
                    @isset($eventSeriesItem->events_min_started_at, $eventSeriesItem->events_max_started_at)
                        <div>
                            <i class="fa fa-fw fa-clock"></i>
                            @if($eventSeriesItem->events_min_started_at->isSameDay($eventSeriesItem->events_max_started_at))
                                {{ formatDate($eventSeriesItem->events_min_started_at) }}
                            @else
                                {{ formatDate($eventSeriesItem->events_min_started_at) }} / {{ formatDate($eventSeriesItem->events_max_started_at) }}
                            @endif
                        </div>
                    @endisset
                    @can('update', $eventSeriesItem)
                        <x-bs::button.group class="mt-3">
                            <x-button.edit href="{{ route('event-series.edit', $eventSeriesItem) }}" class="text-nowrap"/>
                        </x-bs::button.group>
                    @endcan
                </x-bs::list.item>
            @endforeach
        </x-bs::list>
    </div>
@endif
