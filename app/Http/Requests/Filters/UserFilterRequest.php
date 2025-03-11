<?php

namespace App\Http\Requests\Filters;

use App\Enums\ActiveStatus;
use App\Enums\FilterValue;
use App\Http\Requests\Traits\FiltersList;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see User}s
 */
class UserFilterRequest extends FormRequest
{
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
