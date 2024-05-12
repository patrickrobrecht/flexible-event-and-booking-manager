<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\UserController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\User;
use App\Models\UserRole;
use App\Options\ActiveStatus;
use App\Options\FilterValue;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Http\FormRequest;

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
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.email' => $this->ruleForText(),
            'filter.user_role_id' => $this->ruleForAllowedOrExistsInDatabase(UserRole::query(), FilterValue::values()),
            'filter.status' => $this->ruleForAllowedOrExistsInEnum(ActiveStatus::class, [FilterValue::All->value]),
            'sort' => [
                'nullable',
                User::sortOptions()->getRule(),
            ],
        ];
    }
}
