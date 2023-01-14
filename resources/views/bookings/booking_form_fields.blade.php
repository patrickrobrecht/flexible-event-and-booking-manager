@php
    /** @var \App\Models\Booking $booking */
    /** @var \App\Models\BookingOption $bookingOption */
@endphp
<div class="col-12 col-md-6">
    <div class="row">
        <div class="col-12 col-md-6">
            <x-form.row>
                <x-form.label for="first_name">{{ __('First name') }}</x-form.label>
                <x-form.input name="first_name" type="text"
                              :value="$booking->first_name ?? null"/>
            </x-form.row>
        </div>
        <div class="col-12 col-md-6">
            <x-form.row>
                <x-form.label for="last_name">{{ __('Last name') }}</x-form.label>
                <x-form.input name="last_name" type="text"
                              :value="$booking->last_name ?? null"/>
            </x-form.row>
        </div>
    </div>
    <x-form.row>
        <x-form.label for="phone">{{ __('Phone number') }}</x-form.label>
        <x-form.input name="phone" type="tel"
                      :value="$booking->phone ?? null"/>
    </x-form.row>
    <x-form.row>
        <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
        <x-form.input name="email" type="email"
                      :value="$booking->email ?? null"/>
    </x-form.row>

    @include('_shared.address_fields_form', [
        'address' => $booking,
    ])
</div>
