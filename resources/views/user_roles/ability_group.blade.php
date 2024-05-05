@php
    /** @var \App\Models\UserRole $userRole */
    /** @var \App\Options\AbilityGroup[] $abilityGroups */
    /** @var int $headlineLevel */
    $headlineTag = $headlineLevel <= 6 ? "h{$headlineLevel}" : 'strong';
    $childHeadlineLevel = $headlineLevel + 1;
@endphp
@foreach($abilityGroups as $abilityGroup)
    <div class="avoid-break">
        <{{$headlineTag}}>{{ $abilityGroup->getTranslatedName() }}</{{$headlineTag}}>
        @if($editable)
            <x-bs::form.field name="abilities[]" type="switch"
                              :options="\Portavice\Bladestrap\Support\Options::fromEnum($abilityGroup->getAbilities(), 'getTranslatedName')"
                              :value="$userRole->abilities ?? []"/>
        @else
            <ul class="list-unstyled">
                @foreach($abilityGroup->getAbilities() as $ability)
                    @php
                        $hasAbility = in_array($ability->value, $userRole->abilities, true);
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
        'abilityGroups' => $abilityGroup->getChildren(),
        'editable' => $editable,
        'headlineLevel' => $childHeadlineLevel,
    ])
@endforeach
