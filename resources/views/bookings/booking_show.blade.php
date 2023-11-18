@extends('layouts.app')

@php
    /** @var \App\Models\Booking $booking */
    $bookingOption = $booking->bookingOption;
    $event = $bookingOption->event;
@endphp

@section('title')
    {{ __('Booking no. :id', [
        'id' => $booking->id,
    ]) }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-bs::breadcrumb.item>
    @can('viewAny', \App\Models\Booking::class)
        <x-bs::breadcrumb.item href="{{ route('bookings.index', [$event, $bookingOption]) }}">{{ __('Bookings') }}</x-bs::breadcrumb.item>
    @endcan
@endsection

@section('headline')
    <h1>{{ $event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('headline-buttons')
    @can('update', $booking)
        <x-button.edit href="{{ route('bookings.edit', $booking) }}"/>
    @endcan
    @can('viewPDF', $booking)
        <x-bs::button.link variant="secondary" href="{{ route('bookings.show-pdf', $booking) }}">
            <i class="fa fa-file-pdf"></i>
            {{ __('PDF') }}
        </x-bs::button.link>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8">
            @include('bookings.shared.booking_details')

            @isset($bookingOption->form)
                @foreach($bookingOption->form->formFieldGroups as $group)
                    @if($group->show_name)
                        <h2 id="{{ Str::slug($group->name) }}">{{ $group->name }}</h2>
                    @endif
                    @isset($group->description)
                        <p class="lead">{!! $group->description !!}</p>
                    @endisset

                    <div class="row">
                        @foreach($group->formFields as $field)
                            @php
                                $allowedValues = array_combine($field->allowed_values ?? [], $field->allowed_values ?? []);
                                $inputName = $field->input_name . ($field->isMultiCheckbox() ? '[]' : '');
                            @endphp
                            <div class="{{ $field->container_class ?? 'col-12' }}">
                                <x-form.row>
                                    @if($field->type === 'checkbox' && ($field->allowed_values === null || count($field->allowed_values) === 1))
                                        <x-form.input readonly disabled
                                                      name="{{ $field->input_name }}" type="{{ $field->type }}"
                                                      :value="$booking?->getFieldValue($field)">
                                            {{ $field->allowed_values[0] ?? $field->name }}
                                            @if($field->required) * @endif
                                        </x-form.input>
                                    @else
                                        <x-form.label for="{{ $inputName }}">{{ $field->name }} @if($field->required) * @endif</x-form.label>
                                        @if(!$field->required || $field->type === 'checkbox')
                                            <x-form.input readonly disabled
                                                          name="{{ $inputName }}" type="{{ $field->type }}"
                                                          :options="$allowedValues"
                                                          :value="$booking?->getFieldValue($field)" />
                                        @elseif($field->isDate())
                                            <x-form.input readonly disabled
                                                          name="{{ $inputName }}" type="{{ $field->type }}"
                                                          :options="$allowedValues"
                                                          :value="$booking?->getFieldValue($field)?->format('Y-m-d')"
                                                          required />
                                        @else
                                            <x-form.input readonly disabled
                                                          name="{{ $inputName }}" type="{{ $field->type }}"
                                                          :options="$allowedValues"
                                                          :value="$booking?->getFieldValue($field)"
                                                          required />
                                        @endif
                                    @endif
                                    @if(isset($field->hint) && $field->type !== 'hidden')
                                        <div id="{{ $field->id }}-hint" class="form-text">
                                            {!! $field->hint !!}
                                        </div>
                                    @endif
                                </x-form.row>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else {{-- no form set, so use the default form --}}
                <div class="row">
                    <div class="col-12 col-md-6">
                        <x-form.row>
                            <x-form.label for="first_name">{{ __('First name') }}</x-form.label>
                            <x-form.input readonly disabled name="first_name"
                                          value="{{ $booking->first_name }}" />
                        </x-form.row>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-form.row>
                            <x-form.label for="last_name">{{ __('Last name') }}</x-form.label>
                            <x-form.input readonly disabled name="last_name"
                                          value="{{ $booking->last_name }}" />
                        </x-form.row>
                    </div>
                </div>
                <x-form.row>
                    <x-form.label for="phone">{{ __('Phone number') }}</x-form.label>
                    <x-form.input readonly disabled name="phone"
                                  value="{{ $booking->phone ?? null }}" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                    <x-form.input readonly disabled name="email"
                                  value="{{ $booking->email }}" />
                </x-form.row>

                <div class="row">
                    <div class="col-12 col-md-8">
                        <x-form.row>
                            <x-form.label for="street">{{ __('Street') }}</x-form.label>
                            <x-form.input readonly disabled name="street"
                                          value="{{ $booking->street ?? null }}" />
                        </x-form.row>
                    </div>
                    <div class="col-12 col-md-4">
                        <x-form.row>
                            <x-form.label for="house_number">{{ __('House number') }}</x-form.label>
                            <x-form.input readonly disabled name="house_number"
                                          value="{{ $booking->house_number ?? null }}" />
                        </x-form.row>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-4">
                        <x-form.row>
                            <x-form.label for="postal_code">{{ __('Postal code') }}</x-form.label>
                            <x-form.input readonly disabled name="postal_code"
                                          value="{{ $booking->postal_code ?? null }}" />
                        </x-form.row>
                    </div>
                    <div class="col-12 col-md-8">
                        <x-form.row>
                            <x-form.label for="city">{{ __('City') }}</x-form.label>
                            <x-form.input readonly disabled name="city"
                                          value="{{ $booking->city ?? null }}" />
                        </x-form.row>
                    </div>
                </div>
                <x-form.row>
                    <x-form.label for="country">{{ __('Country') }}</x-form.label>
                    <x-form.input readonly disabled name="country"
                                  value="{{ $booking->country ?? null }}" />
                </x-form.row>
            @endisset
        </div>
    </div>

    <x-text.timestamp :model="$booking ?? null"/>
@endsection
