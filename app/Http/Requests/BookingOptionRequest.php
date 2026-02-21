<?php

namespace App\Http\Requests;

use App\Enums\BookingRestriction;
use App\Models\BookingOption;
use App\Models\Event;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * @property Event $event
 * @property ?BookingOption $booking_option
 * @property-read ?string $slug
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
     * @return array<string, array<int, string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        // If a booking option is updated, check for existing confirmed bookings and bookings on the waiting list.
        $confirmedBookingsCount = $this->booking_option?->bookingsConfirmed()->count();
        $bookingsOnWaitingListCount = $this->booking_option?->bookingsOnWaitingList()->count();

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
            'payment_due_days' => [
                'nullable',
                'required_with:price',
                'integer',
                'gte:0',
                'lte:365',
            ],
            'restrictions' => [
                'nullable',
                'array',
            ],
            'restrictions.*' => [
                BookingRestriction::rule(),
            ],
            'maximum_bookings' => [
                'nullable',
                'integer',
                isset($confirmedBookingsCount)
                    ? 'gte:' . $confirmedBookingsCount
                    : 'gte:1',
            ],
            'confirmation_text' => [
                'nullable',
                'string',
            ],
            'waiting_list_places' => [
                'nullable',
                'integer',
                isset($bookingsOnWaitingListCount)
                    ? 'gte:' . $bookingsOnWaitingListCount
                    : 'gte:0',
            ],
            'waiting_list_text' => [
                'nullable',
                'string',
            ],
        ];
    }
}
