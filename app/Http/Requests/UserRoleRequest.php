<?php

namespace App\Http\Requests;

use App\Enums\Ability;
use App\Models\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read ?UserRole $user_role
 */
class UserRoleRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $abilities = $this->input('abilities', []);

        foreach ($abilities as $value) {
            $ability = Ability::tryFrom($value);
            if ($ability) {
                $dependentAbility = $ability->dependsOnAbility();
                if ($dependentAbility && !in_array($dependentAbility->value, $abilities, true)) {
                    $abilities[] = $dependentAbility->value;
                }
            }
        }

        $this->merge(['abilities' => $abilities]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('user_roles', 'name')
                    ->ignore($this->user_role->id ?? null),
            ],
            'abilities' => [
                'sometimes',
                'array',
            ],
            'abilities.*' => [
                Ability::rule(),
            ],
        ];
    }
}
