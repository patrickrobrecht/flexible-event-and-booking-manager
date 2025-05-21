@extends('layouts.app')

@php
    use Portavice\Bladestrap\Support\Options;

    /** @var ?\App\Models\Event $event */
    /** @var \Illuminate\Database\Eloquent\Collection<\App\Models\Event> $events */

    /** @var ?\App\Models\Event $parentEvent */
    $parentEvent = $event->parentEvent ?? null;
    if ($parentEvent === null) {
        $parentEventId = (int) \Portavice\Bladestrap\Support\ValueHelper::getFromQueryOrDefault('parent_event_id');
        $parentEvent = $events->firstWhere('id', '=', $parentEventId);
    }
@endphp

@section('title')
    @isset($event)
        {{ __('Edit :name', ['name' => $event->name]) }}
    @else
        {{ __('Create event') }}
    @endisset
@endsection

@section('breadcrumbs')
    @include('events.shared.event_breadcrumbs')
@endsection

@section('headline-buttons')
    @isset($event)
        @can('forceDelete', $event)
            <x-form.delete-modal :id="$event->id"
                                 :name="$event->name"
                                 :route="route('events.destroy', $event)"/>
        @endcan
    @endisset
@endsection

@section('content')
    <x-bs::form method="{{ isset($event) ? 'PUT' : 'POST' }}"
                action="{{ isset($event) ? route('events.update', $event) : route('events.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
                                  :value="$event->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="slug" type="text" aria-describedby="slugHint"
                                  :value="$event->slug ?? null">
                    {{ __('Slug') }}
                    <x-slot:hint>
                        {!! __('This field defines the path in the URL, such as :url. If you leave it empty, is auto-generated for you.', [
                            'url' => isset($event->slug)
                                ? sprintf('<a href="%s" target="_blank">%s</a>', route('events.show', $event), route('events.show', $event, false))
                                : '<strong>' . route('events.show', Str::of(__('Name of the event'))->snake('-')) . '</strong>'
                        ]) !!}
                    </x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="description" type="textarea"
                                  :value="$event->description ?? null">{{ __('Description') }}</x-bs::form.field>
                <x-bs::form.field name="website_url" type="text"
                                  :value="$event->website_url ?? null"><i class="fa fa-fw fa-display"></i> {{ __('Website') }}</x-bs::form.field>
                <x-bs::form.field name="visibility" type="select"
                                  :options="\App\Enums\Visibility::toOptions()"
                                  :value="$event->visibility->value ?? null"><i class="fa fa-fw fa-eye"></i> {{ __('Visibility') }}</x-bs::form.field>
                <x-bs::form.field name="started_at" type="datetime-local"
                                  :value="isset($event->started_at) ? $event->started_at->format('Y-m-d\TH:i') : null"><i class="fa fa-fw fa-clock"></i> {{ __('Start date') }}</x-bs::form.field>
                <x-bs::form.field name="finished_at" type="datetime-local"
                                  :value="isset($event->finished_at) ? $event->finished_at->format('Y-m-d\TH:i') : null"><i class="fa fa-fw fa-clock"></i> {{ __('End date') }}</x-bs::form.field>
                <x-bs::button.group>
                    <x-button.save>
                        @isset($event)
                            {{ __( 'Save' ) }}
                        @else
                            {{ __('Create') }}
                        @endisset
                    </x-button.save>
                    <x-button.cancel href="{{ route('events.index') }}"/>
                </x-bs::button.group>
            </div>
            <div class="col-12 col-md-6">
                <x-bs::form.field name="location_id" type="select"
                                  :options="$locations->pluck('nameOrAddress', 'id')"
                                  :value="$event->location_id ?? null"><i class="fa fa-fw fa-location-pin"></i> {{ __('Location') }}</x-bs::form.field>
                <x-bs::form.field name="organization_id" type="radio"
                                  :options="Options::fromModels($organizations, 'name')"
                                  :value="$event->organization_id ?? null"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
                <x-bs::form.field name="parent_event_id" type="select"
                                  :options="Options::fromModels($events->except($event->id ?? null), 'name')->prepend(__('none'), '')"
                                  :value="$event->parent_event_id ?? null"
                                  :from-query="\Illuminate\Support\Facades\Request::routeIs('events.create')"><i class="fa fa-fw fa-calendar-days"></i> {{ __('Part of the event') }}</x-bs::form.field>
                <x-bs::form.field name="event_series_id" type="select"
                                  :options="Options::fromModels($eventSeries, 'name')->prepend(__('none'), '')"
                                  :value="$event->event_series_id ?? null"
                                  :from-query="\Illuminate\Support\Facades\Request::routeIs('events.create')"><i class="fa fa-fw fa-calendar-week"></i> {{ __('Part of the event series') }}</x-bs::form.field>

                <h2><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</h2>
                @livewire('users.search-users', [
                    'selectedUsers' => $event->responsibleUsers ?? \Illuminate\Database\Eloquent\Collection::empty(),
                ])
            </div>
        </div>
    </x-bs::form>

    <x-text.timestamp :model="$event ?? null"/>
@endsection
