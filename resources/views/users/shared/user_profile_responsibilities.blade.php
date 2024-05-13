@php
    /** @var \App\Models\User $user */
@endphp
<div class="row">
    <div id="responsibilities" class="col-12 col-xl-6 col-xxl-4">
        <h2><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</h2>
        @if(
            $user->responsibleForEvents->isEmpty()
            && $user->responsibleForEventSeries->isEmpty()
            && $user->responsibleForOrganizations->isEmpty()
        )
            <x-bs::alert class="danger">{{ __(':name has not been assigned any responsibilities.', [
                'name' => $user->first_name,
            ]) }}</x-bs::alert>
        @endif
        @if($user->responsibleForOrganizations->isNotEmpty())
            <div class="mb-3">
                <h3><i class="fa fa-fw fa-sitemap"></i> {{ __('Organizations') }}</h3>
                @include('organizations.shared.organization_list', [
                    'organizations' => $user->responsibleForOrganizations,
                ])
            </div>
        @endif
        @if($user->responsibleForEventSeries->isNotEmpty())
            <div class="mb-3">
                <h3><i class="fa fa-fw fa-calendar-week"></i> {{ __('Event series') }}</h3>
                @include('event_series.shared.event_series_list', [
                    'eventSeries' => $user->responsibleForEventSeries,
                ])
            </div>
        @endif
        @if($user->responsibleForEvents->isNotEmpty())
            <div class="mb-3">
                <h3><i class="fa fa-fw fa-calendar-days"></i> {{ __('Events') }}</h3>
                @include('events.shared.event_list', [
                    'events' => $user->responsibleForEvents,
                ])
            </div>
        @endif
    </div>
    <div id="bookings" class="col-12 col-xl-6 col-xxl-4">
        <h2><i class="fa fa-fw fa-file-contract"></i> {{ __('Bookings') }}</h2>
        @if($user->bookings->count() === 0)
            <x-bs::alert class="danger">{{ __(':name does not have any bookings yet.', [
                'name' => $user->first_name,
            ]) }}</x-bs::alert>
        @endif
        @include('bookings.shared.booking_list', [
            'bookings' => $user->bookings,
        ])
    </div>
    <div id="documents" class="col-12 col-xl-6 col-xxl-4">
        <h2><i class="fa fa-fw fa-file"></i> {{ __('Documents') }}</h2>
        @if($user->documents->count() === 0)
            <x-bs::alert class="danger">{{ __(':name has not uploaded any documents yet.', [
                'name' => $user->first_name,
            ]) }}</x-bs::alert>
        @endif
        @include('documents.shared.document_list', [
            'documents' => $user->documents,
        ])
    </div>
</div>
