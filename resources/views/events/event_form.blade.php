@extends('layouts.app')

@php
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
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($event) ? 'PUT' : 'POST' }}"
            action="{{ isset($event) ? route('events.update', $event) : route('events.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$event->name ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="slug">{{ __('Slug') }}</x-form.label>
                    <x-form.input name="slug" type="text" aria-describedby="slugHint"
                                  :value="$event->slug ?? null"/>
                    <div id="slugHint" class="form-text">
                        {!! __('This field defines the path in the URL, such as :url. If you leave it empty, is auto-generated for you.', [
                            'url' => isset($event->slug)
                                ? sprintf('<a href="%s" target="_blank">%s</a>', route('events.show', $event), route('events.show', $event, false))
                                : '<strong>' . route('events.show', Str::of(__('Name of the event'))->snake('-')) . '</strong>'
                        ]) !!}
                    </div>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="description">{{ __('Description') }}</x-form.label>
                    <x-form.input name="description" type="text"
                                  :value="$event->description ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="website_url">{{ __('Website') }}</x-form.label>
                    <x-form.input name="website_url" type="text"
                                  :value="$event->website_url ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="visibility">{{ __('Visibility') }}</x-form.label>
                    <x-form.select name="visibility"
                                   :options="\App\Options\Visibility::keysWithNames()"
                                   :value="$event->visibility->value ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="started_at">{{ __('Start date') }}</x-form.label>
                    <x-form.input name="started_at"
                                  type="datetime-local"
                                  :value="isset($event->started_at) ? $event->started_at->format('Y-m-d\TH:i') : null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="finished_at">{{ __('End date') }}</x-form.label>
                    <x-form.input name="finished_at"
                                  type="datetime-local"
                                  :value="isset($event->finished_at) ? $event->finished_at->format('Y-m-d\TH:i') : null" />
                </x-form.row>
            </div>
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="location_id">{{ __('Location') }}</x-form.label>
                    <x-form.select name="location_id"
                                   :options="$locations->pluck('nameOrAddress', 'id')"
                                   :value="$event->location_id ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="organization_id">{{ __('Organization') }}</x-form.label>
                    <x-form.input id="organization_id" name="organization_id[]" type="checkbox"
                                  :options="$organizations->pluck('name', 'id')"
                                  :value="isset($event) ? $event->organizations->pluck('id')->toArray() : []"
                                  :valuesToInt="true" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="parent_event_id">{{ __('Part of the event') }}</x-form.label>
                    <x-form.select name="parent_event_id"
                                   :options="$events->except($event->id ?? null)->pluck('name', 'id')"
                                   :value="$event->parent_event_id ?? null">
                        <option value="">{{ __('none') }}</option>
                    </x-form.select>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="event_series_id">{{ __('Part of the event series') }}</x-form.label>
                    <x-form.select name="event_series_id"
                                   :options="$eventSeries->pluck('name', 'id')"
                                   :value="$event->event_series_id ?? null">
                        <option value="">{{ __('none') }}</option>
                    </x-form.select>
                </x-form.row>
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
    </x-form>

    <x-text.timestamp :model="$event ?? null"/>
@endsection
