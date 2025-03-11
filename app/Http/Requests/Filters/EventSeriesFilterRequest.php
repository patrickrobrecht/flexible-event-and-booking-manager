<?php

namespace App\Http\Requests\Filters;

use App\Enums\EventSeriesType;
use App\Enums\FilterValue;
use App\Enums\Visibility;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see EventSeries}s
 */
class EventSeriesFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.visibility' => $this->ruleForAllowedOrExistsInEnum(Visibility::class, [FilterValue::All->value]),
            'filter.event_id' => $this->ruleForAllowedOrExistsInDatabase(Event::query(), FilterValue::values()),
            'filter.organization_id' => $this->ruleForAllowedOrExistsInDatabase(Organization::query(), [FilterValue::All->value]),
            'filter.document_id' => $this->ruleForAllowedOrExistsInDatabase(Document::query(), FilterValue::values()),
            'filter.event_series_type' => $this->ruleForAllowedOrExistsInEnum(EventSeriesType::class, [FilterValue::All->value]),
            'sort' => [
                'nullable',
                EventSeries::sortOptions()->getRule(),
            ],
        ];
    }
}
