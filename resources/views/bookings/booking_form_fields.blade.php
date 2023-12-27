@php
    /** @var bool $canEdit */
    /** @var ?\App\Models\Booking $booking */
    /** @var \App\Models\BookingOption $bookingOption */
@endphp

@if($bookingOption->formFields->isNotEmpty())
    <div class="row">
        @foreach($bookingOption->formFields as $field)
            @if($field->type == 'headline')
                @if($field->required)
                    <h2 id="{{ Str::slug($field->name) }}">{{ $field->name }}</h2>
                @endif
                @isset($field->hint)
                    <p class="lead">{!! $field->hint !!}</p>
                @endisset
            @else
                @php
                    $allowedValues = array_combine($field->allowed_values ?? [], $field->allowed_values ?? []);
                    $inputName = $field->input_name . ($field->isMultiCheckbox() ? '[]' : '');

                    $value = $booking?->getFieldValue($field);
                    if ($field->type === 'hidden') {
                        $value = $field->allowed_values[0] ?? null;
                    } elseif ($field->isDate()) {
                        $value = $value?->format('Y-m-d');
                    } elseif ($field->isSingleCheckbox()) {
                        $allowedValues = [1 => $field->allowed_values[0] ?? $field->name];
                    }
                @endphp
                <x-bs::form.field container-class="{{ $field->container_class ?? 'col-12' }}"
                                  name="{{ $inputName }}" type="{{ $field->type }}"
                                  :options="$allowedValues" :value="$value"
                                  :required="$field->required"
                                  :readonly="!$canEdit" :disabled="!$canEdit">
                    {{ $field->name }} @if($field->required) * @endif
                    @if(isset($field->hint) && $field->type !== 'hidden')
                        <x-slot:hint>{!! $field->hint !!}</x-slot:hint>
                    @endif
                </x-bs::form.field>
            @endif
        @endforeach
    </div>
@else {{-- no form set, so use the default form --}}
    <div class="row">
        <div class="col-12 col-md-6">
            <x-bs::form.field name="first_name" type="text"
                              :value="$booking->first_name ?? null">{{ __('First name') }}</x-bs::form.field>
        </div>
        <div class="col-12 col-md-6">
            <x-bs::form.field name="last_name" type="text"
                              :value="$booking->last_name ?? null">{{ __('Last name') }}</x-bs::form.field>
        </div>
    </div>
    <x-bs::form.field name="phone" type="tel"
                      :value="$booking->phone ?? null">{{ __('Phone number') }}</x-bs::form.field>
    <x-bs::form.field name="email" type="email"
                      :value="$booking->email ?? null">{{ __('E-mail') }}</x-bs::form.field>

    @include('_shared.address_fields_form', [
        'address' => $booking,
    ])
@endif
