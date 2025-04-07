<?php

namespace App\Http\Requests;

use App\Enums\Ability;
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
    protected function prepareForValidation(): void
    {
        $this->merge([
            'abilities' => $this->input('abilities', []), // Force array!
        ]);
    }

    /**
     * @return array<string, array<int, string|Stringable|ValidationRule>>
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
