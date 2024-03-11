@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
@endphp

@section('title')
    {{ $event->name }} | {{ __('Groups') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    @isset($event->parentEvent)
        <x-bs::breadcrumb.item href="{{ route('events.show', $event->parentEvent) }}">{{ $event->parentEvent->name }}</x-bs::breadcrumb.item>
    @endisset
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>{{ __('Groups') }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1>{{ $event->name }}</h1>
@endsection

@section('headline-buttons')
    @can('exportGroups', $event)
        <form method="GET" id="export-form">
            <button type="submit" class="btn btn-primary" name="output" value="export">
                <i class="fa fa-download"></i>
                {{ __('Export') }}
            </button>
        </form>
    @endcan
@endsection

@section('content')
    <livewire:groups.manage-groups :event="$event"/>
@endsection

@push('styles')
    @livewireStyles
@endpush

@push('scripts')
    @livewireScripts
    <script src="{{ mix('/lib/alpinejs.min.js') }}" defer></script>
@endpush
