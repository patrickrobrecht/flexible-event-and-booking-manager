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
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    @isset($bookingOption)
        <x-bs::breadcrumb.item href="{{ route('booking-options.show', [$event, $bookingOption]) }}">
            {{ $bookingOption->name }}
        </x-bs::breadcrumb.item>
    @endisset
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::form method="{{ isset($bookingOption) ? 'PUT' : 'POST' }}"
                action="{{ isset($bookingOption) ? route('booking-options.update', [$event, $bookingOption]) : route('booking-options.store', $event) }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
                                  :value="$bookingOption->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="slug" type="text" :value="$bookingOption->slug ?? null">
                    {{ __('Slug') }}
                    <x-slot:hint>
                        {!! __('This field defines the path in the URL, such as :url. If you leave it empty, is auto-generated for you.', [
                            'url' => isset($bookingOption->slug)
                                ? sprintf('<a href="%s" target="_blank">%s</a>', route('booking-options.show', [$event, $bookingOption]), route('booking-options.show', [$event, $bookingOption], false))
                                : '<strong>' . route('booking-options.show', [Str::of(__('Name of the event'))->snake('-'), Str::of(__('Name of the booking option'))->snake('-')]) . '</strong>'
                        ]) !!}
                    </x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="description" type="text"
                                  :value="$bookingOption->description ?? null">{{ __('Description') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6">
                <x-bs::form.field name="form_id" type="select"
                                  :options="$forms->pluck('name', 'id')->prepend(__('none'), '')"
                                  :value="$bookingOption->form_id ?? null">{{ __('Form') }}</x-bs::form.field>
                <x-bs::form.field name="maximum_bookings" type="number" min="1" step="1"
                                  :value="$bookingOption->maximum_bookings ?? null">
                    {{ __('Maximum bookings') }}
                    @isset($bookingOption)
                        <x-slot:hint>{{ formatTransChoice('Currently :count bookings', $bookingOption->bookings()->count()) }}</x-slot:hint>
                    @endisset
                </x-bs::form.field>
                <x-bs::form.field name="available_from" type="datetime-local"
                                  :value="isset($bookingOption->available_from) ? $bookingOption->available_from->format('Y-m-d\TH:i') : null">{{ __('Start date') }}</x-bs::form.field>
                <x-bs::form.field name="available_until" type="datetime-local"
                                  :value="isset($bookingOption->available_until) ? $bookingOption->available_until->format('Y-m-d\TH:i') : null">{{ __('End date') }}</x-bs::form.field>
                <x-bs::form.field name="price" type="number" min="0.01" step="0.01"
                                  :value="$bookingOption->price ?? null">{{ __('Price') }}</x-bs::form.field>
                <x-bs::form.field id="restrictions" name="restrictions[]" type="switch"
                                  :options="\App\Options\BookingRestriction::toOptions()"
                                  :value="$bookingOption->restrictions ?? null">{{ __('Restrictions') }}</x-bs::form.field>
            </div>
        </div>

        <x-bs::button.group>
            <x-button.save>
                @isset($bookingOption)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('events.show', $event) }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$bookingOption ?? null"/>
@endsection
