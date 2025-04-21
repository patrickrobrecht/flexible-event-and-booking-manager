<?php

namespace App\Http\Requests;

use App\Enums\Ability;
use App\Http\Requests\Traits\ValidatesAbilities;
use App\Models\UserRole;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * @property-read ?UserRole $user_role
 */
class UserRoleRequest extends FormRequest
{
    use ValidatesAbilities;

    protected function getSelectableAbilities(): array
    {
        return Ability::cases();
    }

    /**
     * @return array<string, array<int, string|Stringable>>
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
            ...$this->rulesForAbilities(),
        ];
    }
}
