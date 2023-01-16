@php
    /** @var ?\App\Models\Booking $booking */
    /** @var \App\Models\BookingOption $bookingOption */
@endphp

@isset($bookingOption->form)
    @foreach($bookingOption->form->formFieldGroups as $group)
        @if($group->show_name)
            <h2 id="{{ Str::slug($group->name) }}">{{ $group->name }}</h2>
        @endif
        @isset($group->description)
            <p class="lead">{!! $group->description !!}</p>
        @endisset

        <div class="row">
            @foreach($group->formFields as $field)
                @php
                    $allowedValues = array_combine($field->allowed_values ?? [], $field->allowed_values ?? []);
                    $inputName = $field->input_name . ($field->isMultiCheckbox() ? '[]' : '');
                @endphp
                @if($field->type === 'hidden')
                    <x-form.input name="{{ $field->input_name }}" type="{{ $field->type }}"
                                  :value="$field->allowed_values[0] ?? null" />
                @else
                    <div class="{{ $field->container_class ?? 'col-12' }}">
                        <x-form.row>
                            @if($field->type === 'checkbox' && ($field->allowed_values === null || count($field->allowed_values) === 1))
                                <x-form.input name="{{ $field->input_name }}" type="{{ $field->type }}"
                                              :value="$booking?->getFieldValue($field)">
                                    {{ $field->allowed_values[0] ?? $field->name }}
                                    @if($field->required) * @endif
                                </x-form.input>
                            @else
                                <x-form.label for="{{ $inputName }}">{{ $field->name }} @if($field->required) * @endif</x-form.label>
                                @if(!$field->required || $field->type === 'checkbox')
                                    <x-form.input name="{{ $inputName }}" type="{{ $field->type }}"
                                                  :options="$allowedValues"
                                                  :value="$booking?->getFieldValue($field)" />
                                @else
                                    <x-form.input name="{{ $inputName }}" type="{{ $field->type }}"
                                                  :options="$allowedValues"
                                                  :value="$booking?->getFieldValue($field)"
                                                  required />
                                @endif
                            @endif
                            @if(isset($field->hint) && $field->type !== 'hidden')
                                <div id="{{ $field->id }}-hint" class="form-text">
                                    {!! $field->hint !!}
                                </div>
                            @endif
                        </x-form.row>
                    </div>
                @endif
            @endforeach
        </div>
    @endforeach
@else {{-- no form set, so use the default form --}}
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
@endisset
