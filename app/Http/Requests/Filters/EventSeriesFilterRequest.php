<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Options\EventSeriesType;
use App\Options\FilterValue;
use App\Options\Visibility;
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
            'filter.document_id' => $this->ruleForAllowedOrExistsInDatabase(Document::query(), FilterValue::values()),
            'filter.event_series_type' => $this->ruleForAllowedOrExistsInEnum(EventSeriesType::class, [FilterValue::All->value]),
            'sort' => [
                'nullable',
                EventSeries::sortOptions()->getRule(),
            ],
        ];
    }
}
