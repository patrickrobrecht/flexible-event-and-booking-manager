<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesResponsibleUsers;
use App\Models\Event;
use App\Options\Visibility;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @property ?Event $event
 */
class EventRequest extends FormRequest
{
    use ValidatesResponsibleUsers;

    protected function prepareForValidation(): void
    {
        $this->merge([
            // Replace whitespace etc. with "-"
            'slug' => isset($this->slug) ? Str::slug($this->slug) : null,
        ]);
    }

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
                Rule::exists('events', 'id')
                    ->whereNull('parent_event_id')
                    ->whereNot('id', $this->event->id ?? null),
            ],
            'event_series_id' => [
                'nullable',
                Rule::exists('event_series', 'id'),
            ],
            ...$this->rulesForResponsibleUsers(),
        ];
    }

    public function attributes(): array
    {
        return $this->attributesForResponsibleUsers();
    }
}
