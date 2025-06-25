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
                <x-bs::list.item class="avoid-break">
                    <div><a href="{{ route('events.show', $event->slug) }}" class="fw-bold">{{ $event->name }}</a></div>
                    @isset($event->description)
                        <div class="text-muted">{{ $event->description }}</div>
                    @endisset
                    @include('events.shared.event_badges')
                    @can('viewResponsibilities', $event)
                        <div>
                            <i class="fa fa-fw fa-list-check" title="{{ __('Responsibilities') }}"></i>
                            @include('users.shared.responsible_user_span', [
                                'class' => null,
                                'users' => $event->getResponsibleUsersVisibleForCurrentUser(),
                            ])
                        </div>
                    @endcan
                    <div>
                        <i class="fa fa-fw fa-clock"></i>
                        @include('events.shared.event_dates')
                    </div>
                    <div>
                        <i class="fa fa-fw fa-location-pin"></i>
                        {{ $event->location->nameOrAddress }}
                    </div>
                    @isset($event->website_url)
                        <div>
                            <i class="fa fa-fw fa-display"></i>
                            <a href="{{ $event->website_url }}" target="_blank">{{ __('Website') }}</a>
                        </div>
                    @endisset
                    @canany(['update', 'viewGroups'], $event)
                        <div class="d-flex flex-wrap gap-1 mt-3 d-print-none">
                            @can('update', $event)
                                <x-button.edit href="{{ route('events.edit', $event) }}" class="text-nowrap"/>
                            @endcan
                            @can('viewAny', [\App\Models\Document::class, $event])
                                <x-bs::button.link href="{{ route('events.show', $event) }}#documents" variant="secondary" class="text-nowrap">
                                    <i class="fa fa-fw fa-file"></i> {{ __('Documents') }} <x-bs::badge variant="danger">{{ formatInt($event->documents_count) }}</x-bs::badge>
                                </x-bs::button.link>
                            @endcan
                            @can('viewGroups', $event)
                                <x-bs::button.link href="{{ route('groups.index', $event) }}" variant="secondary" class="text-nowrap">
                                    <i class="fa fa-fw fa-people-group"></i> {{ __('Groups') }} <x-bs::badge variant="danger">{{ formatInt($event->groups_count) }}</x-bs::badge>
                                </x-bs::button.link>
                            @endcan
                        </div>
                    @endcanany
                </x-bs::list.item>
            @endcan
        @endforeach
    </div>
@endif
