<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\UserController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\User;
use App\Options\ActiveStatus;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Filter for {@see User}s
 */
class UserFilterRequest extends FormRequest
{
    /** {@see UserPolicy} in {@see UserController} */
    use AuthorizationViaController;
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.email' => $this->ruleForText(),
            'filter.user_role_id' => [
                'nullable',
                Rule::exists('user_roles', 'id'),
            ],
            'filter.status' => [
                'nullable',
                ActiveStatus::rule()
            ],
        ];
    }
}
