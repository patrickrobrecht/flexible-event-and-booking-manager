<?php

namespace App\Http\Requests;

use App\Models\BookingOption;
use App\Models\Event;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * @property-read Event $event
 * @property-read BookingOption $booking_option
 */
class BookingPaymentRequest extends FormRequest
{
    /**
     * @return array<string, array<int, Closure|string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        /** @var int[] $bookingsNotPaidYet */
        $bookingsNotPaidYet = $this->booking_option->bookings()
            ->whereNull('paid_at')
            ->pluck('id')
            ->toArray();

        return [
            'booking_id' => [
                'required',
                'array',
                function ($attribute, $value, $fail) use ($bookingsNotPaidYet) {
                    /** @var int[] $bookingIds */
                    $bookingIds = array_map('intval', $value ?? []);
                    if (!$this->containsOnlyValidIds($bookingIds, $bookingsNotPaidYet)) {
                        $fail(__('validation.not_in', ['attribute' => __('validation.attributes.booking_id')]));
                    }
                },
            ],
            'booking_id.*' => [
                'integer',
                Rule::in($bookingsNotPaidYet),
            ],
            'paid_at' => [
                'required',
                'date_format:Y-m-d\TH:i',
            ],
        ];
    }

    /**
     * @param int[] $selectedIds
     * @param int[] $allowedIds
     */
    public function containsOnlyValidIds(array $selectedIds, array $allowedIds): bool
    {
        foreach ($selectedIds as $selectedId) {
            if (!in_array($selectedId, $allowedIds, true)) {
                return false;
            }
        }

        return true;
    }
}
