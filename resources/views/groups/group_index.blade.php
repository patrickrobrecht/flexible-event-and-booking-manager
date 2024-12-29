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
        <x-bs::button type="submit" name="output" value="export" form="export-form">
            <i class="fa fa-download"></i> {{ __('Export') }}
        </x-bs::button>
    @endcan
    @can('create', \App\Models\Group::class)
        <x-bs::modal.button modal="generateGroupsModal">
            <i class="fa fa-arrows-rotate"></i> {{ __('Generate groups') }}
        </x-bs::modal.button>
    @endcan
    @can('forceDeleteAny', \App\Models\Group::class)
        <x-bs::modal.button variant="danger" modal="deleteAllGroupsModal">
            <i class="fa fa-minus-circle"></i> {{ __('Delete ALL groups') }}
        </x-bs::modal.button>
    @endcan
@endsection

@section('content')
    <livewire:groups.manage-groups :event="$event"/>

    @can('exportGroups', $event)
        <form method="GET" id="export-form"></form>
    @endcan
    @can('create', \App\Models\Group::class)
        @php
            /** @var \Illuminate\Support\ViewErrorBag $validationErrorsForGeneration */
            $validationErrorsForGeneration = $errors->generate ?? null;
        @endphp
        <x-bs::modal id="generateGroupsModal" :centered="true" :static-backdrop="true" :close-button-title="__('Cancel')">
            <x-slot:title>{{ __('Generate groups') }}</x-slot:title>
            <x-bs::form id="generate-form" method="POST" action="{{ route('groups.generate', $event) }}">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <x-bs::form.field name="method" :error-bag="$validationErrorsForGeneration"
                                          type="radio" :options="\App\Options\GroupGenerationMethod::toOptions()">{{ __('Method') }}</x-bs::form.field>
                        <x-bs::form.field name="groups_count" :error-bag="$validationErrorsForGeneration"
                                          type="number" min="1" step="1">{{ __('Number of groups') }}</x-bs::form.field>
                    </div>
                    <div class="col-12 col-sm-6">
                        @php
                            $bookingOptions = \Portavice\Bladestrap\Support\Options::fromModels(
                                $event->getBookingOptions(),
                                fn (\App\Models\BookingOption $bookingOption) => sprintf(
                                    '%s (%s)',
                                    $bookingOption->name,
                                    formatInt($bookingOption->bookings_count)
                                )
                            );
                        @endphp
                        <x-bs::form.field name="booking_option_id[]" :error-bag="$validationErrorsForGeneration"
                                          type="checkbox" :options="$bookingOptions">{{ __('Booking options') }}</x-bs::form.field>
                        @if($event?->parentEvent?->groups->isNotEmpty())
                            <x-bs::form.field name="exclude_parent_group_id[]" :error-bag="$validationErrorsForGeneration"
                                              type="checkbox" :options="\Portavice\Bladestrap\Support\Options::fromModels($event->parentEvent->groups, 'name')">
                                {{ __('Exclude members of groups') }}</x-bs::form.field>
                        @endif
                    </div>
                </div>
            </x-bs::form>
            <x-slot:footer>
                <x-bs::button type="submit" form="generate-form">
                    <i class="fa fa-fw fa-arrows-rotate"></i> {{ __('Generate groups') }}
                </x-bs::button>
            </x-slot:footer>
        </x-bs::modal>
        @if(isset($validationErrorsForGeneration) && $validationErrorsForGeneration->isNotEmpty())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    (new bootstrap.Modal(document.getElementById('generateGroupsModal'))).show();
                });
            </script>
        @endif
    @endcan
    @can('forceDeleteAny', \App\Models\Group::class)
        @php
            /** @var \Illuminate\Support\ViewErrorBag $validationErrorsForGeneration */
            $validationErrorsForDeletion = $errors->delete ?? null;
        @endphp
        <x-bs::modal id="deleteAllGroupsModal" :centered="true" :static-backdrop="true" :close-button-title="__('Cancel')">
            <x-slot:title>{{ __('Delete ALL groups') }}</x-slot:title>
            <x-bs::form id="delete-form" method="DELETE" action="{{ route('groups.deleteAll', $event) }}">
                {{ __('Are you sure you want to delete all groups of the event ":name"?', [
                    'name' => $event->name,
                ]) }}
                <x-bs::form.field name="name" :error-bag="$validationErrorsForDeletion"
                                  type="text" maxlength="255">{{ __('Name of the event') }}</x-bs::form.field>
            </x-bs::form>
            <x-slot:footer>
                <x-bs::button type="submit" form="delete-form" variant="danger">
                    <i class="fa fa-fw fa-circle-minus"></i> {{ __('Delete ALL groups') }}
                </x-bs::button>
            </x-slot:footer>
        </x-bs::modal>
        @if(isset($validationErrorsForDeletion) && $validationErrorsForDeletion->isNotEmpty())
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    (new bootstrap.Modal(document.getElementById('deleteAllGroupsModal'))).show();
                });
            </script>
        @endif
    @endcan
@endsection

@push('styles')
    @livewireStyles
@endpush

@push('scripts')
    @livewireScripts
@endpush
