@php
    /** @var \App\Enums\Ability[] $selectedAbilities */
    /** @var \App\Enums\AbilityGroup[] $abilityGroups */
    /** @var int $headlineLevel */
    $headlineTag = $headlineLevel <= 6 ? "h{$headlineLevel}" : 'strong';
    $childHeadlineLevel = $headlineLevel + 1;
@endphp
@foreach($abilityGroups as $abilityGroup)
    <div class="avoid-break">
        <{{$headlineTag}}><i class="{{ $abilityGroup->getIcon() }}"></i> {{ $abilityGroup->getTranslatedName() }}</{{$headlineTag}}>
        @if($editable)
            @php
                $abilitiesOptions = \Portavice\Bladestrap\Support\Options::fromEnum(
                    $abilityGroup->getAbilities(),
                    'getTranslatedName',
                    static function (\App\Enums\Ability $ability) {
                        $dependency = $ability->dependsOnAbility();

                        if ($dependency === null) {
                            return [];
                        }

                        return [
                            'data-depends-on-id' => 'abilities[]-' . $dependency->value,
                        ];
                    }
                );
            @endphp
            <x-bs::form.field name="abilities[]" type="switch" :options="$abilitiesOptions"
                              check-container-class="mb-3"
                              :value="$selectedAbilities ?? []"/>
        @else
            <ul class="list-unstyled">
                @foreach($abilityGroup->getAbilities() as $ability)
                    @php
                        $hasAbility = in_array($ability->value, $selectedAbilities, true);
                    @endphp
                    <li @class([
                        'fw-bold' => $hasAbility,
                    ])><i @class([
                        'fa fa-fw',
                        $hasAbility ? 'fa-check text-success' : 'fa-xmark text-danger',
                    ])></i> {{ $ability->getTranslatedName() }}</li>
                @endforeach
            </ul>
        @endif
        @include('user_roles.ability_group', [
            'selectedAbilities' => $selectedAbilities,
            'abilityGroups' => $abilityGroup->getChildren(),
            'editable' => $editable,
            'headlineLevel' => $childHeadlineLevel,
        ])
    </div>
@endforeach

@once
    @push('scripts')
        <script src="{{ mix('js/ability-dependencies.js') }}"></script>
    @endpush
@endonce
