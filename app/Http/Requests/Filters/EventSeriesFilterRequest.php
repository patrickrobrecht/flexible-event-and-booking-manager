<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\EventSeriesController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Options\EventSeriesType;
use App\Options\Visibility;
use App\Policies\EventSeriesPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see EventSeries}s
 */
class EventSeriesFilterRequest extends FormRequest
{
    /** {@see EventSeriesPolicy} in {@see EventSeriesController} */
    use AuthorizationViaController;
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.visibility' => [
                'nullable',
                Visibility::rule(),
            ],
            'filter.event_id' => $this->ruleForAllowedOrExists(Event::query(), ['+', '-']),
            'filter.document_id' => $this->ruleForAllowedOrExists(Document::query(), ['+', '-']),
            'filter.event_series_type' => [
                'nullable',
                EventSeriesType::rule(),
            ],
            'sort' => [
                'nullable',
                EventSeries::sortOptions()->getRule(),
            ],
        ];
    }
}
