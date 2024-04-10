<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\EventController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Event;
use App\Options\EventType;
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
            'filter.visibility' => [
                'nullable',
                Visibility::rule(),
            ],
            'filter.date_from' => $this->ruleForDate(),
            'filter.date_until' => $this->ruleForDate('filter.date_from'),
            'filter.location_id' => $this->ruleForForeignId('locations'),
            'filter.organization_id' => $this->ruleForForeignId('organizations'),
            'filter.event_type' => [
                'nullable',
                EventType::rule(),
            ],
            'sort' => [
                'nullable',
                Event::sortOptions()->getRule(),
            ],
        ];
    }
}
