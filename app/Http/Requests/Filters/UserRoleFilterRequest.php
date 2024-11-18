<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\Traits\FiltersList;
use App\Models\User;
use App\Models\UserRole;
use App\Options\FilterValue;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see UserRole}s
 */
class UserRoleFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.user_id' => $this->ruleForAllowedOrExistsInDatabase(User::query(), FilterValue::values()),
            'sort' => [
                'nullable',
                UserRole::sortOptions()->getRule(),
            ],
        ];
    }
}
