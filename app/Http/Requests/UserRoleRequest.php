<?php

namespace App\Http\Requests;

use App\Http\Controllers\UserRoleController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Models\UserRole;
use App\Options\Ability;
use App\Policies\UserRolePolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read ?UserRole $user_role
 */
class UserRoleRequest extends FormRequest
{
    /** {@see UserRolePolicy} in {@see UserRoleController} */
    use AuthorizationViaController;

    protected function prepareForValidation(): void
    {
        $this->merge([
            'abilities' => $this->input('abilities', []), // Force array!
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
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
