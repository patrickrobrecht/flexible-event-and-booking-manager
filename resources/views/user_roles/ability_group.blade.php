@php
    /** @var \App\Options\Ability[] $selectedAbilities */
    /** @var \App\Options\AbilityGroup[] $abilityGroups */
    /** @var int $headlineLevel */
    $headlineTag = $headlineLevel <= 6 ? "h{$headlineLevel}" : 'strong';
    $childHeadlineLevel = $headlineLevel + 1;
@endphp
@foreach($abilityGroups as $abilityGroup)
    <div class="avoid-break">
        <{{$headlineTag}}><i class="fa fa-fw {{ $abilityGroup->getIcon() }}"></i> {{ $abilityGroup->getTranslatedName() }}</{{$headlineTag}}>
        @if($editable)
            <x-bs::form.field name="abilities[]" type="switch"
                              :options="\Portavice\Bladestrap\Support\Options::fromEnum($abilityGroup->getAbilities(), 'getTranslatedName')"
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
    </div>
    @include('user_roles.ability_group', [
        'selectedAbilities' => $selectedAbilities,
        'abilityGroups' => $abilityGroup->getChildren(),
        'editable' => $editable,
        'headlineLevel' => $childHeadlineLevel,
    ])
@endforeach
