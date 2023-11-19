@php
    $address = $address ?? $location ?? $user ?? null;
@endphp
<div class="row">
    <div class="col-12 col-md-8">
        <x-bs::form.field name="street" type="text"
                          :value="$address->street ?? null">{{ __('Street') }}</x-bs::form.field>
    </div>
    <div class="col-12 col-md-4">
        <x-bs::form.field name="house_number" type="text"
                          :value="$address->house_number ?? null">{{ __('House number') }}</x-bs::form.field>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-4">
        <x-bs::form.field name="postal_code" type="text"
                          :value="$address->postal_code ?? null">{{ __('Postal code') }}</x-bs::form.field>
    </div>
    <div class="col-12 col-md-8">
        <x-bs::form.field name="city" type="text"
                          :value="$address->city ?? null">{{ __('City') }}</x-bs::form.field>
    </div>
</div>
<x-bs::form.field name="country" type="text"
                  :value="$address->country ?? null">{{ __('Country') }}</x-bs::form.field>
