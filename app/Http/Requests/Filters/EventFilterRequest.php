<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\EventController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Event;
use App\Policies\EventPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Event}s
 */
class EventFilterRequest extends FormRequest
{
    /** {@see EventPolicy} in {@see EventController} */
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
            'filter.organization_id' => $this->ruleForForeignId('organizations'),
        ];
    }
}
