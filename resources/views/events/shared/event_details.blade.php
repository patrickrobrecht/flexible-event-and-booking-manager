@php
    /** @var \App\Models\Event $event */
@endphp

<x-list.group>
    @isset($event->description)
        <li class="list-group-item">
            {{ $event->description }}
        </li>
    @endisset
    @isset($event->website_url)
        <li class="list-group-item">
            <a href="{{ $event->website_url }}" target="_blank">{{ __('Website') }}</a>
        </li>
    @endisset
    <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                    </span>
        <div><x-badge.visibility :visibility="$event->visibility"/></div>
    </li>
    <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-clock" title="{{ __('Date') }}"></i>
                    </span>
        <div>@include('events.shared.event_dates')</div>
    </li>
    <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-location-pin" title="{{ __('Address') }}"></i>
                    </span>
        <div>
            @foreach($event->location->fullAddressBlock as $line)
                {{ $line }}@if(!$loop->last)
                    <br>
                @endif
            @endforeach
        </div>
    </li>
    <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-sitemap" title="{{ __('Organizations') }}"></i>
                    </span>
        <div>
            @if($event->organizations->count() === 0)
                {{ __('none') }}
            @else
                <ul class="list-unstyled">
                    @foreach($event->organizations as $organization)
                        <li>{{ $organization->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </li>
</x-list.group>
