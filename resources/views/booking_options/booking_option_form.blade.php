@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
    /** @var ?\App\Models\BookingOption $bookingOption */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Form $forms */
@endphp

@section('title')
    @isset($bookingOption)
        {{ __('Edit :name', ['name' => $bookingOption->name]) }}
    @else
        {{ __('Create booking option') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $event) }}">{{ $event->name }}</x-nav.breadcrumb>
    @isset($bookingOption)
        <x-nav.breadcrumb href="{{ route('booking-options.show', [$event, $bookingOption]) }}">
            {{ $bookingOption->name }}
        </x-nav.breadcrumb>
    @endisset
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($bookingOption) ? 'PUT' : 'POST' }}"
            action="{{ isset($bookingOption) ? route('booking-options.update', [$event, $bookingOption]) : route('booking-options.store', $event) }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$bookingOption->name ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="slug">{{ __('Slug') }}</x-form.label>
                    <x-form.input name="slug" type="text" aria-describedby="slugHint"
                                  :value="$bookingOption->slug ?? null"/>
                    <div id="slugHint" class="form-text">
                        {!! __('This field defines the path in the URL, such as :url. If you leave it empty, is auto-generated for you.', [
                            'url' => isset($bookingOption->slug)
                                ? sprintf('<a href="%s" target="_blank">%s</a>', route('booking-options.show', [$event, $bookingOption]), route('booking-options.show', [$event, $bookingOption], false))
                                : '<strong>' . route('booking-options.show', [Str::of(__('Name of the event'))->snake('-'), Str::of(__('Name of the booking option'))->snake('-')]) . '</strong>'
                        ]) !!}
                    </div>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="description">{{ __('Description') }}</x-form.label>
                    <x-form.input name="description" type="text"
                                  :value="$bookingOption->description ?? null"/>
                </x-form.row>
            </div>
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="form_id">{{ __('Form') }}</x-form.label>
                    <x-form.select name="form_id"
                                   :options="$forms->pluck('name', 'id')"
                                   :value="$bookingOption->form_id ?? null">
                        <option value="">{{ __('none') }}</option>
                    </x-form.select>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="maximum_bookings">{{ __('Maximum bookings') }}</x-form.label>
                    <x-form.input name="maximum_bookings" type="number" min="1" step="1"
                                  :value="$bookingOption->maximum_bookings ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="available_from">{{ __('Start date') }}</x-form.label>
                    <x-form.input name="available_from" type="datetime-local"
                                  :value="isset($bookingOption->available_from) ? $bookingOption->available_from->format('Y-m-d\TH:i') : null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="available_until">{{ __('End date') }}</x-form.label>
                    <x-form.input name="available_until" type="datetime-local"
                                  :value="isset($bookingOption->available_until) ? $bookingOption->available_until->format('Y-m-d\TH:i') : null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="price">{{ __('Price') }}</x-form.label>
                    <x-form.input name="price" type="number" min="0.01" step="0.01"
                                  :value="$bookingOption->price ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="restrictions">{{ __('Restrictions') }}</x-form.label>
                    <x-form.input id="restrictions" name="restrictions[]" type="checkbox"
                                  :options="\App\Options\BookingRestriction::keysWithNames()"
                                  :value="$bookingOption->restrictions ?? null"/>
                </x-form.row>
            </div>
        </div>

        <x-button.group>
            <x-button.save>
                @isset($bookingOption)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('events.show', $event) }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$bookingOption ?? null"/>
@endsection
