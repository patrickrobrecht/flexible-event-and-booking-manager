@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
@endphp

@section('title')
    {{ $event->name }} | {{ __('Groups') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>{{ __('Bookings') }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1>{{ $event->name }}</h1>
@endsection

@section('content')
    <livewire:groups.manage-groups :event="$event"/>
@endsection