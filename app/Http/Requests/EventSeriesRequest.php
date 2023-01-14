<?php

namespace App\Http\Requests;

use App\Http\Controllers\EventSeriesController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Models\EventSeries;
use App\Options\Visibility;
use App\Policies\EventSeriesPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property ?EventSeries $event_series
 */
class EventSeriesRequest extends FormRequest
{
    /** {@see EventSeriesPolicy} in {@see EventSeriesController} */
    use AuthorizationViaController;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
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
            'parent_event_series_id' => [
                'nullable',
                Rule::prohibitedIf(fn () => isset($this->event_series)
                                            && $this->event_series->subEventSeries->count() > 0),
                Rule::exists('event_series', 'id')
                    ->whereNull('parent_event_series_id')
                    ->whereNot('id', $this->event_series->id ?? null),
            ],
        ];
    }
}
