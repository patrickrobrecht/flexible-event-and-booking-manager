@php
    /** @var \App\Models\User $user */
@endphp
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
