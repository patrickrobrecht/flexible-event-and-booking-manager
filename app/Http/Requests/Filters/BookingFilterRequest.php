<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\BookingController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Booking;
use App\Options\PaymentStatus;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
        $groupExistsRule = Rule::exists('groups', 'id');
        if (isset($this->event)) {
            $groupExistsRule->where('event_id', $this->event->id);
        }

        return [
            'filter.search' => $this->ruleForText(),
            'filter.payment_status' => [
                'nullable',
                PaymentStatus::rule(),
            ],
            'filter.group_id' => [
                'nullable',
                $groupExistsRule,
            ],
            'sort' => [
                'nullable',
                Booking::sortOptions()->getRule(),
            ],
        ];
    }
}
