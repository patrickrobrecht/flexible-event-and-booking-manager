<?php

namespace App\Http\Requests\Filters;

use App\Enums\EventType;
use App\Enums\FilterValue;
use App\Enums\Visibility;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Stringable;

/**
 * Filter for {@see Event}s
 */
class EventFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * @return array<string, array<int, Closure|ValidationRule|string|Stringable>>
     */
    public function rules(): array
    {
        return [
            'filter.search' => $this->ruleForText(),
            'filter.visibility' => $this->ruleForAllowedOrExistsInEnum(Visibility::class, [FilterValue::All->value]),
            'filter.date_from' => $this->ruleForDate(),
            'filter.date_until' => $this->ruleForDate('filter.date_from'),
            'filter.event_series_id' => $this->ruleForAllowedOrExistsInDatabase(EventSeries::query(), FilterValue::values()),
            'filter.organization_id' => $this->ruleForAllowedOrExistsInDatabase(Organization::query(), [FilterValue::All->value]),
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
