@extends('layouts.app')

@php
    use Portavice\Bladestrap\Support\Options;

    /** @var ?\App\Models\Event $event */
@endphp

@section('title')
    @isset($event)
        {{ __('Edit :name', ['name' => $event->name]) }}
    @else
        {{ __('Create event') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    @isset($event->parentEvent)
        <x-bs::breadcrumb.item href="{{ route('events.show', $event->parentEvent) }}">{{ $event->parentEvent->name }}</x-bs::breadcrumb.item>
    @endisset
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
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
                <x-bs::form.field name="description" type="text"
                                  :value="$event->description ?? null">{{ __('Description') }}</x-bs::form.field>
                <x-bs::form.field name="website_url" type="text"
                                  :value="$event->website_url ?? null">{{ __('Website') }}</x-bs::form.field>
                <x-bs::form.field name="visibility" type="select"
                                  :options="\App\Options\Visibility::toOptions()"
                                  :value="$event->visibility->value ?? null">{{ __('Visibility') }}</x-bs::form.field>
                <x-bs::form.field name="started_at" type="datetime-local"
                                  :value="isset($event->started_at) ? $event->started_at->format('Y-m-d\TH:i') : null">{{ __('Start date') }}</x-bs::form.field>
                <x-bs::form.field name="finished_at" type="datetime-local"
                                  :value="isset($event->finished_at) ? $event->finished_at->format('Y-m-d\TH:i') : null">{{ __('End date') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6">
                <x-bs::form.field name="location_id" type="select"
                                  :options="$locations->pluck('nameOrAddress', 'id')"
                                  :value="$event->location_id ?? null">{{ __('Location') }}</x-bs::form.field>
                <x-bs::form.field id="organization_id" name="organization_id[]" type="checkbox"
                                  :options="Options::fromModels($organizations, 'name')"
                                  :value="isset($event) ? $event->organizations->pluck('id')->toArray() : []">{{ __('Organization') }}</x-bs::form.field>
                <x-bs::form.field name="parent_event_id" type="select"
                                  :options="Options::fromModels($events->except($event->id ?? null), 'name')->prepend(__('none'), '')"
                                  :value="$event->parent_event_id ?? null"
                                  :from-query="\Illuminate\Support\Facades\Request::routeIs('events.create')">{{ __('Part of the event') }}</x-bs::form.field>
                <x-bs::form.field name="event_series_id" type="select"
                                  :options="Options::fromModels($eventSeries, 'name')->prepend(__('none'), '')"
                                  :value="$event->event_series_id ?? null">{{ __('Part of the event series') }}</x-bs::form.field>
            </div>
        </div>

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
    </x-bs::form>

    <x-text.timestamp :model="$event ?? null"/>
@endsection
