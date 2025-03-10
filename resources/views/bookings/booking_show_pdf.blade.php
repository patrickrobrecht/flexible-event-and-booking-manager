@php
    /** @var \App\Models\Booking $booking */
    $bookingOption = $booking->bookingOption;
    $event = $bookingOption->event;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $bookingOption->name }} {{ $booking->name }}</title>
    <style>
        body {
            font-family: Helvetica, serif;
        }

        table {
            width: 100%;
        }

        h1, h2 {
            margin-top: 4px;
            margin-bottom: 2px;
        }

        td {
            padding: 2px;
        }

        .label {
            width: 7cm;
            vertical-align: top;
        }

        .underline {
            border-bottom: 1px solid;
        }

        .small {
            font-size: .875em;
        }

        .d-print-none {
            display: none;
        }
    </style>
</head>
<body>
    <p>{{ __('Booking no. :id', [
        'id' => $booking->id,
    ]) }} • <strong>{{ $bookingOption->name }}</strong></p>
    <h1>{{ $booking->name }}</h1>

    <table>
        <tbody>
            <tr>
                <td class="label">{{ __('Event') }}</td>
                <td>{{ $event->name }}
                    <br>@include('events.shared.event_dates')
                    <br>@foreach($event->location->fullAddressBlock as $line)
                        {{ $line }}@if(!$loop->last)
                            <br>
                        @endif
                    @endforeach</td>
            </tr>
            <tr>
                <td class="label">{{ __('Booking date') }}</td>
                <td>
                    @isset($booking->booked_at)
                        {{ formatDateTime($booking->booked_at) }}
                    @else
                        {{ __('Booking not completed yet') }}
                    @endisset
                </td>
            </tr>
            <tr>
                <td class="label">{{ __('Booked by') }}</td>
                <td>
                    @isset($booking->bookedByUser)
                        {{ $booking->bookedByUser->name }}
                        @isset($booking->bookedByUser->email_verified_at)
                            <br/>{{ $booking->bookedByUser->email }} <span class="small">({{ __('verified at :date', [
                                'date' => formatDateTime($booking->bookedByUser->email_verified_at)
                            ]) }})</span>
                        @endisset
                    @else
                        {{ __('Guest') }}
                    @endisset
                </td>
            </tr>
            <tr>
                <td class="label">{{ __('Price') }}</td>
                <td>
                    @isset($booking->price)
                        {{ formatDecimal($booking->price) }}&nbsp;€
                        @isset($booking->paid_at)
                            <strong>{{ __('paid') }}</strong> ({{ $booking->paid_at->isMidnight()
                                ? formatDate($booking->paid_at)
                                : formatDateTime($booking->paid_at) }})
                        @else
                            <span style="color: red;">{{ __('not paid yet') }}</span>
                        @endisset
                    @else
                        {{ __('free of charge') }}
                    @endisset
                </td>
            </tr>
        </tbody>
    </table>

    @if($bookingOption->formFields->isNotEmpty())
        <table>
            <tbody>
            @foreach($bookingOption->formFields as $field)
                @if($field->type->isStatic())
                    <tr>
                        <td colspan="2">
                            <h2>{{ $field->name }}</h2>
                            <div class="small">{!! $field->hint !!}</div>
                        </td>
                    </tr>
                @else
                    @php
                        $label = $field->name;
                        if ($field->isSingleCheckbox()) {
                            $label = $field->allowed_values[0] ?? $field->name;
                        }

                        $value = $booking->getFieldValueAsText($field) ?? '—';
                        if ($field->type === \App\Options\FormElementType::File) {
                            $value = isset($value) ? __('File uploaded.') : __('No file uploaded.');
                        }
                    @endphp
                    <tr>
                        <td class="label">{{ $label }}</td>
                        <td class="underline">{{ $value }}</td>
                    </tr>
                @endif
            @endforeach
            </tbody>
        </table>
    @else
        {{-- default form --}}
        <h2></h2>{{-- force margin --}}
        <table>
            <tbody>
            <tr>
                <td class="label">{{ __('First name') }}</td>
                <td class="underline">{{ $booking->first_name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Last name') }}</td>
                <td class="underline">{{ $booking->last_name }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Phone number') }}</td>
                <td class="underline">{{ $booking->phone ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('E-mail') }}</td>
                <td class="underline">{{ $booking->email }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Street') }}</td>
                <td class="underline">{{ $booking->street ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('House number') }}</td>
                <td class="underline">{{ $booking->house_number ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Postal code') }}</td>
                <td class="underline">{{ $booking->postal_code ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('City') }}</td>
                <td class="underline">{{ $booking->city ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">{{ __('Country') }}</td>
                <td class="underline">{{ $booking->country ?? '-' }}</td>
            </tr>
            </tbody>
        </table>
    @endif
</body>
</html>
