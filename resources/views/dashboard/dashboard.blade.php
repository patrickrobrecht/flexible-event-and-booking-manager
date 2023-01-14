@extends('layouts.app')

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>{{ __('Next events') }}</h2>
            @include('events.shared.event_list', [
                'events' => $events,
                'showVisibility' => false,
            ])
        </div>
    </div>
@endsection
