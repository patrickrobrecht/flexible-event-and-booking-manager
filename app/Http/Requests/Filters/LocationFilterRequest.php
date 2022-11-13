<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Location;
use App\Policies\LocationPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Location}s
 */
class LocationFilterRequest extends FormRequest
{
    /** {@see LocationPolicy} in {@see LocationController} */
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
            'filter.address' => $this->ruleForText(),
        ];
    }
}
