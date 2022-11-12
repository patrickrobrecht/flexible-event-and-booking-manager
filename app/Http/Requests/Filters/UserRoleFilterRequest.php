<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\UserRoleController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Policies\UserRolePolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see UserRole}s
 */
class UserRoleFilterRequest extends FormRequest
{
    /** {@see UserRolePolicy} in {@see UserRoleController} */
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
        ];
    }
}
