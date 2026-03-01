@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
@endphp

@section('title')
    {{ $event->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\Event::class)
        <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Events') }}</x-bs::breadcrumb.item>
    @endcan
    @isset($event->parentEvent)
        @can('view', $event->parentEvent)
            <x-bs::breadcrumb.item href="{{ route('events.show', $event->parentEvent) }}">{{ $event->parentEvent->name }}</x-bs::breadcrumb.item>
        @else
            <x-bs::breadcrumb.item>{{ $event->parentEvent->name }}</x-bs::breadcrumb.item>
        @endcan
    @endisset
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $event)
        <x-button.edit href="{{ route('events.edit', $event) }}"/>
    @endcan
    @can('viewGroups', $event)
        <x-bs::button.link href="{{ route('groups.index', $event) }}" variant="secondary">
            <i class="fa fa-fw fa-people-group"></i> {{ __('Groups') }}
            <x-bs::badge variant="danger">{{ formatInt($event->groups_count) }}</x-bs::badge>
        </x-bs::button.link>
    @endcan
    @can('forceDelete', $event)
        <x-form.delete-modal :id="$event->id"
                             :name="$event->name"
                             :route="route('events.destroy', $event)"/>
    @endcan
@endsection

@section('content')
    @include('events.shared.event_badges')

    <div class="row my-3">
        <div class="col-12 col-lg-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-lg-8 mt-4 mt-lg-0">
            @php
                $bookingOptionsToShow = $event->bookingOptions->isNotEmpty()
                    || Auth::user()?->can('create', [\App\Models\BookingOption::class, $event])
            @endphp
            @if($bookingOptionsToShow)
                @if($event->bookingOptions->count() > 0)
                    <x-bs::list>
                        @include('events.shared.event_booking_options')
                    </x-bs::list>
                @endif
                @can('create', [\App\Models\BookingOption::class, $event])
                    <div @class([
                        'mt-3' => $event->bookingOptions->isNotEmpty(),
                        'd-print-none',
                    ])>
                        <x-bs::button.link href="{{ route('booking-options.create', $event) }}">
                            <i class="fa fa-fw fa-plus"></i> {{ __('Create booking option') }}
                        </x-bs::button.link>
                    </div>
                @endcan
            @endif

            @php
                $responsibilitySectionEmpty = true;
            @endphp
            @can('viewResponsibilities', $event)
                <section id="responsibilities" @class([
                    'mt-4' => $bookingOptionsToShow,
                ])>
                    @php
                        $responsibilitySectionEmpty = false;
                    @endphp
                    <h2><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</h2>
                    @include('users.shared.responsible_user_list', [
                        'users' => $event->getResponsibleUsersVisibleForCurrentUser(),
                    ])
                </section>
            @endcan

            @php
                $documentSectionEmpty = true;
            @endphp
            @canany(['viewAny', 'create'], [\App\Models\Document::class, $event])
                @php
                    $documentSectionEmpty = false;
                @endphp
                <section id="documents" @class([
                    'mt-4' => $bookingOptionsToShow || !$responsibilitySectionEmpty,
                ])>
                    <div class="d-flex justify-content-between align-items-center">
                        <h2><i class="fa fa-fw fa-file"></i> {{ __('Documents') }}</h2>
                        @if($event->hasImages())
                            <x-bs::button.link href="{{ route('events.gallery', $event) }}" variant="secondary">
                                <i class="fa fa-fw fa-images"></i> {{ __('Image gallery') }}
                            </x-bs::button.link>
                        @endif
                    </div>
                    @can('viewAny', [\App\Models\Document::class, $event])
                        @include('documents.shared.document_list', [
                            'documents' => $event->documents,
                        ])
                    @endcan
                    @include('documents.shared.document_add_modal', [
                        'reference' => $event,
                        'routeForAddDocument' => route('events.documents.store', $event),
                    ])
            @endcanany

            @isset($event->parentEvent)
                @php
                    $siblingEvents = $event->parentEvent->subEvents
                        ->each(fn (\App\Models\Event $e) => $e->setRelation('parentEvent', $event->parentEvent))
                        ->keyBy('id')
                        ->except($event->id);
                @endphp
                @if($siblingEvents->isNotEmpty())
                    <section id="events" @class([
                        'mt-4' => $bookingOptionsToShow || !$responsibilitySectionEmpty || !$documentSectionEmpty,
                    ])>
                        <h2><i class="fa fa-fw fa-calendar-days"></i> {{ __('Other sub events of :name', [
                            'name' => $event->parentEvent->name,
                        ]) }}</h2>
                        @include('events.shared.event_list', [
                            'events' => $siblingEvents,
                            'showParentEvent' => false,
                        ])
                    </section>
                @endif
            @else
                @php
                    $subEvents = $event->subEvents
                        ->each(fn (\App\Models\Event $e) => $e->setRelation('parentEvent', $event))
                        ->filter(fn (\App\Models\Event $subEvent) => \Illuminate\Support\Facades\Gate::check('view', $subEvent));
                @endphp
                @if($subEvents->isNotEmpty() || Auth::user()?->can('createChild', $event))
                    <section id="events" @class([
                        'mt-4' => $bookingOptionsToShow || !$documentSectionEmpty,
                    ])>
                        <h2><i class="fa fa-fw fa-calendar-days"></i> {{ __('Sub events') }}</h2>
                        @if($subEvents->isNotEmpty())
                            @include('events.shared.event_list', [
                                'events' => $event->subEvents,
                                'showParentEvent' => false,
                            ])
                        @endif
                        @can('createChild', $event)
                            <div class="mt-3 d-print-none">
                                <x-bs::button.link href="{{ route('events.create', ['parent_event_id' => $event->id, 'location_id' => $event->location->id, 'organization_id' => $event->organization->id]) }}">
                                    <i class="fa fa-fw fa-plus"></i> {{ __('Create event') }}
                                </x-bs::button.link>
                            </div>
                        @endcan
                    </section>
                @endif
            @endif
        </div>
    </div>

    @can('update', $event)
        <x-text.updated-human-diff :model="$event"/>
    @endcan
@endsection
