<?php

namespace App\Http\Requests\Traits;

use App\Enums\Ability;
use Illuminate\Foundation\Http\FormRequest;
use Stringable;

/**
 * @mixin FormRequest
 */
trait ValidatesAbilities
{
    /**
     * @return Ability[]
     */
    abstract protected function getSelectableAbilities(): array;

    protected function prepareForValidation(): void
    {
        $this->prepareAbilitiesForValidation();
    }

    protected function prepareAbilitiesForValidation(): void
    {
        /** @var string[] $abilities */
        $abilities = $this->input('abilities', []);

        foreach ($abilities as $value) {
            $ability = Ability::tryFrom($value);
            if ($ability !== null) {
                $dependentAbility = $ability->dependsOnAbility();
                if (
                    $dependentAbility !== null
                    // dependent ability is not selected yet
                    && !in_array($dependentAbility->value, $abilities, true)
                    // but is selectable
                    && in_array($dependentAbility, $this->getSelectableAbilities(), true)
                ) {
                    $abilities[] = $dependentAbility->value;
                }
            }
        }

        $this->merge(['abilities' => $abilities]);
    }

    /**
     * @return array<string, array<int, string|Stringable>>
     */
    protected function rulesForAbilities(): array
    {
        return [
            'abilities' => [
                'sometimes',
                'array',
            ],
            'abilities.*' => [
                Ability::rule($this->getSelectableAbilities()),
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return $this->attributesForAbilities();
    }

    /**
     * @return array<string, string>
     */
    protected function attributesForAbilities(): array
    {
        return [
            'abilities.*' => __('validation.attributes.abilities'),
        ];
    }
}
