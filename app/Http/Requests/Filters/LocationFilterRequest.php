<?php

namespace App\Http\Requests\Filters;

use App\Enums\FilterValue;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Event;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Location}s
 */
class LocationFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.address' => $this->ruleForText(),
            'filter.event_id' => $this->ruleForAllowedOrExistsInDatabase(Event::query(), FilterValue::values()),
            'filter.organization_id' => $this->ruleForAllowedOrExistsInDatabase(Organization::query(), FilterValue::values()),
            'sort' => [
                'nullable',
                Location::sortOptions()->getRule(),
            ],
        ];
    }
}
