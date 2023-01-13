@extends('layouts.app')

@php
    /** @var \App\Models\BookingOption $bookingOption */
@endphp

@section('title')
    {{ $bookingOption->event->name }} | {{ $bookingOption->name }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $bookingOption->event) }}">{{ $bookingOption->event->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb>{{ $bookingOption->name }}</x-nav.breadcrumb>
@endsection

@section('headline')
    <hgroup>
        <h1>{{ $bookingOption->name }}</h1>
        <h2>{{ $bookingOption->event->name }}</h2>
    </hgroup>
@endsection

@section('headline-buttons')
    @can('update', $bookingOption)
        <x-button.edit href="{{ route('booking-options.edit', [$event, $bookingOption]) }}"/>
    @endcan
@endsection

@section('content')

@endsection
