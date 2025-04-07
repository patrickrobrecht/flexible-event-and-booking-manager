<?php

namespace App\Http\Requests\Filters;

use App\Enums\FilterValue;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Event;
use App\Models\Location;
use App\Models\Organization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Stringable;

/**
 * Filter for {@see Location}s
 */
class LocationFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * @return array<string, array<int, Closure|ValidationRule|string|Stringable>>
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
