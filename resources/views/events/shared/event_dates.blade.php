@php
    /** @var \App\Models\Event */
@endphp

@if(isset($event->started_at, $event->finished_at) && $event->started_at->isSameDay($event->finished_at))
    {{ __(':start until :end', [
        'start' => formatDateTime($event->started_at),
        'end' => formatTime($event->finished_at),
    ]) }}
@else
    {{ __(':start until :end', [
        'start' => isset($event->started_at) ? formatDateTime($event->started_at) : '?',
        'end' => isset($event->finished_at) ? formatDateTime($event->finished_at) : '?',
    ]) }}
@endif
