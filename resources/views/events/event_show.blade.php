@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
@endphp

@section('title')
    {{ $event->name }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    @can('update', $event)
        <div class="text-end">
            <x-button.edit href="{{ route('events.edit', $event) }}"/>
        </div>
    @endcan
    <div class="col-12 col-md-6 mb-3">
        <x-list.group class="list-group-flush">
            <x-list.item>
                <span>
                    <i class="fa fa-fw fa-clock"></i>
                    {{ __('Date') }}
                </span>
                <span class="text-end">
                    {{ __(':start until :end', [
                        'start' => isset($event->started_at) ? formatDateTime($event->started_at) : '?',
                        'end' => isset($event->finished_at) ? formatDateTime($event->finished_at) : '?',
                    ]) }}
                </span>
            </x-list.item>
            <x-list.item>
                <span>
                    <i class="fa fa-fw fa-location-pin"></i>
                    {{ __('Address') }}
                </span>
                <span class="text-end">
                    @foreach($event->location->fullAddressBlock as $line)
                        {{ $line }}@if(!$loop->last)
                            <br>
                        @endif
                    @endforeach
                </span>
            </x-list.item>
            <x-list.item>
                <span>
                    <i class="fa fa-fw fa-sitemap"></i>
                    {{ __('Organizations') }}
                </span>
                <span class="text-end">
                    <ul class="list-unstyled">
                        @foreach($event->organizations as $organization)
                            <li>{{ $organization->name }}</li>
                        @endforeach
                    </ul>
                </span>
            </x-list.item>
        </x-list.group>
    </div>

    <x-text.updated-human-diff :model="$event"/>
@endsection
