<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesBelongsToOrganization;
use App\Http\Requests\Traits\ValidatesResponsibleUsers;
use App\Models\EventSeries;
use App\Options\Visibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property ?EventSeries $event_series
 */
class EventSeriesRequest extends FormRequest
{
    use ValidatesBelongsToOrganization;
    use ValidatesResponsibleUsers;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $organization = $this->getOrganizationFromRequest();

        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('event_series', 'slug')
                    ->ignore($this->event_series ?? null),
            ],
            'visibility' => [
                'required',
                Visibility::rule(),
            ],
            'organization_id' => [
                'required',
                Rule::exists('organizations', 'id'),
            ],
            'parent_event_series_id' => [
                'nullable',
                Rule::prohibitedIf(fn () => isset($this->event_series) && $this->event_series->subEventSeries->count() > 0),
                function ($attribute, $value, $fail) use ($organization) {
                    if (!isset($value)) {
                        return;
                    }

                    $parentEventSeries = EventSeries::query()->find($value);
                    if (!isset($parentEventSeries) || $parentEventSeries->id === $this->event_series->id || $parentEventSeries->parent_event_series_id !== null) {
                        $fail(__('validation.exists', [
                            'attribute' => __('validation.attributes.parent_event_series_id'),
                        ]));
                        return;
                    }

                    if (isset($parentEventSeries, $organization) && $parentEventSeries->organization_id !== $organization->id) {
                        $fail(__('validation.organization', [
                            'attribute' => __('validation.attributes.parent_event_series_id'),
                            'organization' => $organization->name,
                        ]));
                    }
                },
            ],
            ...$this->rulesForResponsibleUsers(),
        ];
    }

    public function attributes(): array
    {
        return $this->attributesForResponsibleUsers();
    }
}
