@php
    /** @var bool $canEdit */
    /** @var ?\App\Models\Booking $booking */
    /** @var \App\Models\BookingOption $bookingOption */
@endphp

@if($bookingOption->formFields->isNotEmpty())
    <div class="row">
        @foreach($bookingOption->formFields as $field)
            @if($field->type->isStatic())
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
                    $required = $field->required;
                    if ($field->type === \App\Enums\FormElementType::Hidden) {
                        $value = $field->allowed_values[0] ?? null;
                    } elseif ($field->isDate()) {
                        $value = $value?->format('Y-m-d');
                    } elseif ($field->isSingleCheckbox()) {
                        $allowedValues = \Portavice\Bladestrap\Support\Options::one($field->allowed_values[0] ?? $field->name);
                    } elseif ($field->type === \App\Enums\FormElementType::File) {
                        $required = $required && !isset($value);
                    }
                @endphp
                <x-bs::form.field container-class="{{ $field->container_class ?? 'col-12' }} avoid-break"
                                  name="{{ $inputName }}" type="{{ $field->type->value }}"
                                  :options="$allowedValues" :value="$value"
                                  :disabled="!$canEdit" :readonly="!$canEdit" :required="$required">
                    {{ $field->name }}
                    @if(isset($field->hint) && $field->type !== \App\Enums\FormElementType::Hidden)
                        <x-slot:hint>{!! $field->hint !!}</x-slot:hint>
                    @endif
                    @if($field->type === \App\Enums\FormElementType::File && isset($booking))
                        <x-slot:appendText :container="!isset($value)">
                            @isset($value)
                                @php
                                    $formFieldValue = $booking?->formFieldValues
                                        ->first(
                                            static fn (\App\Models\FormFieldValue $formFieldValue) => $formFieldValue->formField->column === null
                                                && $formFieldValue->formField->is($field)
                                        );
                                @endphp
                                <x-bs::button.link variant="primary" href="{{ route('bookings.show-file', [$booking, $formFieldValue]) }}">
                                    <i class="fa fa-fw fa-download"></i> {{ __('Download file') }}
                                </x-bs::button.link>
                            @else
                                {{ __('No file uploaded.') }}
                            @endif
                        </x-slot:appendText>
                    @endif
                </x-bs::form.field>
            @endif
        @endforeach
    </div>
@else
    {{-- no form set, so use the default form --}}
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
    <div class="row">
        <div class="col-12 col-md-6">
            <x-bs::form.field name="date_of_birth" type="date"
                              :value="$booking?->date_of_birth?->format('Y-m-d') ?? null">{{ __('Date of birth') }}</x-bs::form.field>
        </div>
        <div class="col-12 col-md-6">
            <x-bs::form.field name="phone" type="tel"
                              :value="$booking->phone ?? null">{{ __('Phone number') }}</x-bs::form.field>
        </div>
    </div>
    <x-bs::form.field name="email" type="email"
                      :value="$booking->email ?? null">{{ __('E-mail') }}</x-bs::form.field>

    @include('_shared.address_fields_form', [
        'address' => $booking,
    ])
@endif
