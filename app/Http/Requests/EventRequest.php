<?php

namespace App\Http\Requests;

use App\Enums\Visibility;
use App\Http\Requests\Traits\ValidatesBelongsToOrganization;
use App\Http\Requests\Traits\ValidatesResponsibleUsers;
use App\Models\Event;
use App\Models\EventSeries;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * @property ?Event $event
 */
class EventRequest extends FormRequest
{
    use ValidatesBelongsToOrganization;
    use ValidatesResponsibleUsers;

    protected function prepareForValidation(): void
    {
        $this->merge([
            // Replace whitespace etc. with "-"
            'slug' => isset($this->slug) ? Str::slug($this->slug) : null,
        ]);
    }

    /**
     * @return array<string, array<int, Closure|string|Stringable|ValidationRule>>
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
                Rule::unique('events', 'slug')
                    ->ignore($this->event ?? null),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'visibility' => [
                'required',
                Visibility::rule(),
            ],
            'started_at' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
                'required_with:finished_at',
            ],
            'finished_at' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],
            'website_url' => [
                'nullable',
                'url:http,https',
                'max:255',
            ],
            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),
            ],
            'organization_id' => [
                'required',
                Rule::exists('organizations', 'id'),
            ],
            'parent_event_id' => [
                'nullable',
                Rule::prohibitedIf(fn () => isset($this->event) && $this->event->subEvents->count() > 0),
                function ($attribute, $value, $fail) use ($organization) {
                    if (!isset($value)) {
                        return;
                    }

                    /** @var ?Event $parentEvent */
                    $parentEvent = Event::query()->find($value);
                    if (!isset($parentEvent) || $parentEvent->id === $this->event?->id || $parentEvent->parent_event_id !== null) {
                        $fail(__('validation.exists', [
                            'attribute' => __('validation.attributes.parent_event_id'),
                        ]));
                        return;
                    }

                    if (isset($organization) && $parentEvent->organization_id !== $organization->id) {
                        $fail(__('validation.organization', [
                            'attribute' => __('validation.attributes.parent_event_id'),
                            'organization' => $organization->name,
                        ]));
                    }
                },
            ],
            'event_series_id' => [
                'nullable',
                function ($attribute, $value, $fail) use ($organization) {
                    if (!isset($value)) {
                        return;
                    }

                    /** @var ?EventSeries $eventSeries */
                    $eventSeries = EventSeries::query()->find($value);
                    if (!isset($eventSeries)) {
                        $fail(__('validation.exists', [
                            'attribute' => __('validation.attributes.event_series_id'),
                        ]));
                        return;
                    }

                    if (isset($organization) && $eventSeries->organization_id !== $organization->id) {
                        $fail(__('validation.organization', [
                            'attribute' => __('validation.attributes.event_series_id'),
                            'organization' => $organization->name,
                        ]));
                    }
                },
            ],
            ...$this->rulesForResponsibleUsers(),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return $this->attributesForResponsibleUsers();
    }
}
