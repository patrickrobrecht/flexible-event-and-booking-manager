<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\OrganizationController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Organization;
use App\Policies\OrganizationPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Organization}s
 */
class OrganizationFilterRequest extends FormRequest
{
    /** {@see OrganizationPolicy} in {@see OrganizationController} */
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
            'filter.location_id' => $this->ruleForForeignId('locations'),
        ];
    }
}
