<?php

namespace App\Http\Requests\Filters;

use App\Enums\DeletedFilter;
use App\Enums\FilterValue;
use App\Enums\PaymentStatus;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\Group;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * Filter for {@see Booking}s.
 *
 * @property-read Event $event
 * @property-read BookingOption $booking_option
 */
class BookingFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * @return array<string, array<int, Closure|string|Stringable|ValidationRule>>
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
