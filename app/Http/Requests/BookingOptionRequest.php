<?php

namespace App\Http\Requests;

use App\Models\BookingOption;
use App\Models\Event;
use App\Options\BookingRestriction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @property Event $event
 * @property ?BookingOption $booking_option
 */
class BookingOptionRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            // Replace whitespace etc. with "-"
            'slug' => isset($this->slug) ? Str::slug($this->slug) : null,
            'restrictions' => $this->restrictions ?? [],
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
                Rule::unique('booking_options', 'slug')
                    ->where('event_id', $this->event->id)
                    ->ignore($this->booking_option ?? null),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'maximum_bookings' => [
                'nullable',
                'integer',
                'gte:1',
            ],
            'available_from' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
                'required_with:available_until',
            ],
            'available_until' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],
            'price' => [
                'nullable',
                'numeric',
                'gte:0',
                'lte:999999.99',
                Rule::prohibitedIf(!isset($this->event->organization->iban)),
            ],
            'book_for_self_only' => [
                'nullable',
                'bool',
            ],
            'restrictions' => [
                'nullable',
                'array',
            ],
            'restrictions.*' => [
                BookingRestriction::rule(),
            ],
        ];
    }
}
