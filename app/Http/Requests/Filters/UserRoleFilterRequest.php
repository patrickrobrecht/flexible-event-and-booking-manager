<?php

namespace App\Http\Requests\Filters;

use App\Enums\FilterValue;
use App\Http\Requests\Traits\FiltersList;
use App\Models\User;
use App\Models\UserRole;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see UserRole}s.
 */
class UserRoleFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * @return array<string, array<int, Closure|string|ValidationRule>>
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
