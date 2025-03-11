<?php

namespace App\Http\Requests\Filters;

use App\Enums\FilterValue;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Models\Event;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Organization}s
 */
class OrganizationFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.event_id' => $this->ruleForAllowedOrExistsInDatabase(Event::query(), FilterValue::values()),
            'filter.location_id' => $this->ruleForAllowedOrExistsInDatabase(Location::query(), [FilterValue::All->value]),
            'filter.document_id' => $this->ruleForAllowedOrExistsInDatabase(Document::query(), FilterValue::values()),
            'sort' => [
                'nullable',
                Organization::sortOptions()->getRule(),
            ],
        ];
    }
}
