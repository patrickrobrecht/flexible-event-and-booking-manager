<?php

namespace App\Http\Requests;

use App\Http\Controllers\BookingController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\ValidatesAddressFields;
use App\Models\Booking;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property ?Booking $booking
 */
class BookingRequest extends FormRequest
{
    /** {@see BookingPolicy} in {@see BookingController} */
    use AuthorizationViaController;
    use ValidatesAddressFields;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $rules = [
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'max:255',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^([0-9\s\ \+\(\)]*)$/'
            ],
            'email' => [
                'required',
                'email',
                'max:255',
            ],
        ];

        $addressFieldsRule = 'nullable';
        return array_replace($rules, $this->rulesForAddressFields($addressFieldsRule));
    }
}
