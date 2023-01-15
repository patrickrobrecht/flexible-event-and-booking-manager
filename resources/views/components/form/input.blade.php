@props([
    'id',
    'name',
    'type' => 'text',
    'prependText' => null,
    'appendText' => null,
    'value' => '',
    'options' => null,
    'valuesToInt' => false,
    'errorBag' => $errors,
    'containerClass' => '', {{-- only for checkbox and radio --}}
    'selectorForSelectAll' => null,
    'selectorForUnselectAll' => null,
])
@php
    $fieldName = fieldNameToArray($name);

    $isCheckOrRadio = in_array($type, ['checkbox', 'radio'], true);
    if (count(old()) > 0) {
        $old = old(fieldNameToArray($name));
        if ($isCheckOrRadio) {
            $old = $old ?? [];
            $value = $valuesToInt
                ? array_map(static fn ($i) => (int)$i, $old)
                : $old;
        } elseif (!is_array($old)) {
            $value = $old;
        }
    } elseif (\App\Helpers\QueryInput::hasAnyOrDefault()) {
        $oldQuery = \App\Helpers\QueryInput::old(fieldNameToArray($name));
        if ($isCheckOrRadio) {
            $oldQuery = $oldQuery ?? [];
            $value = $valuesToInt
                ? array_map(static fn ($i) => (int)$i, $oldQuery)
                : $oldQuery;
        } elseif (!is_array($oldQuery)) {
            $value = $oldQuery;
        }
    }
@endphp
@if($prependText || $appendText)
    <div class="input-group has-validation">
@endif
        @if($prependText)
            <label for="{{ $id ?? $name }}" class="input-group-text">
                {{ $prependText }}
            </label>
        @endif
        @if($isCheckOrRadio)
            @if($options === null)
                {{-- single option --}}
                @php
                    $selected = (int)$value === 1;
                @endphp
                <div class="form-check">
                    <input {{ $attributes->class(['form-check-input', 'is-invalid' => $errorBag->hasAny($fieldName)]) }}
                           type="{{ $type }}" value="1"
                           id="{{ $id ?? $name }}" name="{{ $name }}"
                           @if($selected) checked @endif />
                    <label class="form-check-label" for="{{ $id ?? $name }}">{{ $slot }}</label>

                    <x-form.feedback name="{{ $name }}" :errorBag="$errorBag"/>
                </div>
            @else
                {{-- At least one option --}}
                <div class="{{ $containerClass }}">
                    @foreach($options as $optionValue => $optionLabel)
                        @php
                            $optionId = ($id ?? $name) . '-' . $optionValue;
                            $selected = is_array($value)
                                ? in_array($optionValue, $value, true)
                                : $optionValue === $value;
                            $hasErrors = $errorBag->hasAny([$fieldName, $fieldName . '.*']);
                        @endphp
                        <div class="form-check">
                            <input {{ $attributes->class(['form-check-input', 'is-invalid' => $hasErrors]) }}
                                   type="{{ $type }}" value="{{ $optionValue }}"
                                   id="{{ $optionId }}" name="{{ $name }}"
                                   @if($selected) checked @endif />
                            <label class="form-check-label" for="{{ $optionId }}">{{ $optionLabel }}</label>

                            @if($loop->last)
                                <x-form.feedback name="{{ $name }}" :errorBag="$errorBag" :showSubErrors="true"/>
                            @endif
                        </div>
                    @endforeach
                    @if(count($options) % 2 === 1 && $containerClass !== '')
                        <div class="mb-5"></div>
                    @endif
                </div>
                @if($selectorForSelectAll || $selectorForUnselectAll)
                    @once
                        @push('scripts')
                            <script src="{{ mix('js/components.js') }}"></script>
                        @endpush
                    @endonce
                    @isset($selectorForSelectAll)
                        <x-form.button class="select-all-button" type="button"
                                       data-target="{{ $selectorForSelectAll }}"
                                       data-action="select">
                            {{ __('Select all') }}
                        </x-form.button>
                    @endisset
                    @isset($selectorForUnselectAll)
                        <x-form.button class="select-all-button" type="button"
                                       data-target="{{ $selectorForUnselectAll }}"
                                       data-action="unselect">
                            {{ __('Unselect all') }}
                        </x-form.button>
                    @endisset
                @endif
            @endif
        @elseif($type === 'textarea')
            <textarea id="{{ $id ?? $name }}" name="{{ $name }}"
                {{ $attributes->class(['form-control', 'is-invalid' => $errorBag->hasAny($fieldName)]) }}>{{--
                --}}{{ $value }}</textarea>
        @else
            {{-- Other type than checkbox, radio, textarea --}}
            <input id="{{ $id ?? $name }}" name="{{ $name }}"
                   type="{{ $type }}" value="{{ $value }}"
                   {{ $attributes->class(['form-control', 'is-invalid' => $errorBag->hasAny($fieldName)]) }} />
        @endif
        @if($appendText)
            <label for="{{ $id ?? $name }}" class="input-group-text">
                {{ $appendText }}
            </label>
        @endif
        @if(!$isCheckOrRadio)
            <x-form.feedback name="{{ $name }}" :errorBag="$errorBag"/>
        @endif
@if($prependText || $appendText)
    </div>
@endif
