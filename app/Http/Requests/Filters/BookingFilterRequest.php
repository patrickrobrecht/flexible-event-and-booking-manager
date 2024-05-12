<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\BookingController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Booking;
use App\Models\Group;
use App\Options\FilterValue;
use App\Options\PaymentStatus;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Booking}s
 */
class BookingFilterRequest extends FormRequest
{
    /** {@see BookingPolicy} in {@see BookingController} */
    use AuthorizationViaController;
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
            'sort' => [
                'nullable',
                Booking::sortOptions()->getRule(),
            ],
        ];
    }
}
