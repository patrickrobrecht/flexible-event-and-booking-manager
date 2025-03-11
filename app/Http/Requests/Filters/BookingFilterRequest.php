<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\Traits\FiltersList;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\Group;
use App\Options\DeletedFilter;
use App\Options\FilterValue;
use App\Options\PaymentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Filter for {@see Booking}s
 *
 * @property-read Event $event
 * @property-read BookingOption $booking_option
 */
class BookingFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $groupQuery = Group::query();
        if (isset($this->event)) {
            $groupQuery->where('event_id', $this->event->id);
        }

        return [
            'filter.search' => $this->ruleForText(),
            'filter.payment_status' => $this->ruleForAllowedOrExistsInEnum(PaymentStatus::class, [FilterValue::All->value]),
            'filter.group_id' => $this->ruleForAllowedOrExistsInDatabase($groupQuery, [FilterValue::All->value]),
            'filter.trashed' => [
                'nullable',
                DeletedFilter::rule(),
            ],
            'sort' => [
                'nullable',
                Booking::sortOptions()->getRule(),
            ],
            'output' => [
                'nullable',
                Rule::in([
                    'export',
                    'pdf',
                    ...$this->booking_option
                        ->formFieldsForFiles
                        ->pluck('id')
                        ->toArray(),
                ]),
            ],
        ];
    }
}
