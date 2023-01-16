@extends('layouts.app')

@php
    /** @var ?\App\Models\Form $form */
@endphp

@section('title')
    @isset($form)
        {{ __('Edit :name', ['name' => $form->name]) }}
    @else
        {{ __('Create form') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('forms.index') }}">{{ __('Forms') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($form) ? 'PUT' : 'POST' }}"
            action="{{ isset($form) ? route('forms.update', $form) : route('forms.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$form->name ?? null"/>
                </x-form.row>
            </div>
            @isset($form)
                <div class="col-12 col-md-6">
                    <x-list.group>
                        @foreach($form->bookingOptions as $bookingOption)
                            <li class="list-group-item">
                                <div>
                                    <a href="{{ route('events.show', $bookingOption->event) }}">{{ $bookingOption->event->name }}</a>
                                    (<a href="{{ route('booking-options.show', [$bookingOption->event, $bookingOption]) }}">{{ $bookingOption->name }}</a>)
                                </div>
                                @include('booking_options.shared.booking_option_period')
                            </li>
                        @endforeach
                    </x-list.group>
                </div>
            @endisset
        </div>

        <x-button.group>
            <x-button.save>
                @isset($form)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('forms.index') }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$form ?? null"/>
@endsection
