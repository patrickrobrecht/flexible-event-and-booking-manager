<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\EventController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use App\Options\EventType;
use App\Options\FilterValue;
use App\Options\Visibility;
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
     */
    public function rules(): array
    {
        return [
            'filter.search' => $this->ruleForText(),
            'filter.visibility' => $this->ruleForAllowedOrExistsInEnum(Visibility::class, [FilterValue::All->value]),
            'filter.date_from' => $this->ruleForDate(),
            'filter.date_until' => $this->ruleForDate('filter.date_from'),
            'filter.event_series_id' => $this->ruleForAllowedOrExistsInDatabase(EventSeries::query(), FilterValue::values()),
            'filter.organization_id' => $this->ruleForAllowedOrExistsInDatabase(Organization::query(), FilterValue::values()),
            'filter.location_id' => $this->ruleForAllowedOrExistsInDatabase(Location::query(), [FilterValue::All->value]),
            'filter.document_id' => $this->ruleForAllowedOrExistsInDatabase(Document::query(), FilterValue::values()),
            'filter.event_type' => $this->ruleForAllowedOrExistsInEnum(EventType::class, [FilterValue::All->value]),
            'sort' => [
                'nullable',
                Event::sortOptions()->getRule(),
            ],
        ];
    }
}
