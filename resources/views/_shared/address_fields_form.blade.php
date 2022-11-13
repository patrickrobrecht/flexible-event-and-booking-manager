@php
    $address = $address ?? $location ?? $user ?? null;
@endphp
<div class="row">
    <div class="col-12 col-md-8">
        <x-form.row>
            <x-form.label for="street">{{ __('Street') }}</x-form.label>
            <x-form.input name="street" type="text"
                          :value="$address->street ?? null" />
        </x-form.row>
    </div>
    <div class="col-12 col-md-4">
        <x-form.row>
            <x-form.label for="house_number">{{ __('House number') }}</x-form.label>
            <x-form.input name="house_number" type="text"
                          :value="$address->house_number ?? null" />
        </x-form.row>
    </div>
</div>
<div class="row">
    <div class="col-12 col-md-4">
        <x-form.row>
            <x-form.label for="postal_code">{{ __('Postal code') }}</x-form.label>
            <x-form.input name="postal_code" type="text"
                          :value="$address->postal_code ?? null" />
        </x-form.row>
    </div>
    <div class="col-12 col-md-8">
        <x-form.row>
            <x-form.label for="city">{{ __('City') }}</x-form.label>
            <x-form.input name="city" type="text"
                          :value="$address->city ?? null" />
        </x-form.row>
    </div>
</div>
<x-form.row>
    <x-form.label for="country">{{ __('Country') }}</x-form.label>
    <x-form.input name="country" type="text"
                  :value="$address->country ?? null" />
</x-form.row>
