@php
    /** @var \App\Enums\Ability[] $selectableAbilities */
    $selectableAbilities = $selectableAbilities ?? \App\Enums\Ability::cases();
    /** @var \App\Enums\Ability[] $selectedAbilities */
    /** @var \App\Enums\AbilityGroup[] $abilityGroups */
    /** @var int $headlineLevel */
    $headlineTag = $headlineLevel <= 6 ? "h{$headlineLevel}" : 'strong';
    $childHeadlineLevel = $headlineLevel + 1;
@endphp
@foreach($abilityGroups as $abilityGroup)
    @php
        $abilitiesInGroup = $abilityGroup->filterAbilities($selectableAbilities);
        if (count($abilitiesInGroup) === 0 && !$abilityGroup->hasChildrenWithAbilities($selectableAbilities)) {
            continue;
        }
    @endphp
    <div class="avoid-break">
        <{{$headlineTag}}><i class="{{ $abilityGroup->getIcon() }}"></i> {{ $abilityGroup->getTranslatedName() }}</{{$headlineTag}}>
        @if($editable)
            @php
                $abilitiesOptions = \Portavice\Bladestrap\Support\Options::fromEnum(
                    $abilitiesInGroup,
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
                @foreach($abilitiesInGroup as $ability)
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
            'selectableAbilities' => $selectableAbilities,
            'selectedAbilities' => $selectedAbilities,
            'abilityGroups' => $abilityGroup->getChildren(),
            'editable' => $editable,
            'headlineLevel' => $childHeadlineLevel,
        ])
    </div>
@endforeach

@once
    @push('scripts')
        @vite(['resources/js/ability-dependencies.js'])
    @endpush
@endonce
