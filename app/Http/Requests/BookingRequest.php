<?php

namespace App\Http\Requests;

use App\Http\Controllers\BookingController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\ValidatesAddressFields;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Policies\BookingPolicy;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read BookingOption $booking_option
 * @property-read ?Booking $booking
 */
class BookingRequest extends FormRequest
{
    /** {@see BookingPolicy} in {@see BookingController} */
    use AuthorizationViaController;
    use ValidatesAddressFields;

    public function prepareForValidation()
    {
        if (isset($this->booking->bookingOption)) {
            // Set booking option from booking when updating an existing booking.
            $this->booking_option = $this->booking->bookingOption;
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $defaultRules = [
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

        $user = $this->user();
        $commonRules = [];
        if (isset($user, $this->booking)) {
            $commonRules = [
                'paid_at' => [
                    $user->can('updatePaymentStatus', $this->booking) ? 'nullable' : 'prohibited',
                    'date_format:Y-m-d\TH:i',
                ],
                'comment' => [
                    $user->can('updateBookingComment', $this->booking) ? 'nullable' : 'prohibited',
                    'string',
                ],
            ];
        }

        if (!isset($this->booking_option->form)) {
            return array_replace($defaultRules, $commonRules, $this->rulesForAddressFields('nullable'));
        }

        $rules = [];
        foreach ($this->booking_option->form->formFieldGroups as $group) {
            foreach ($group->formFields as $field) {
                $rulesForField = [
                    $field->required ? 'required' : 'nullable',
                ];
                $allowedValues = $field->allowed_values ?? [];

                if ($field->type === 'checkbox' && count($allowedValues) > 1) {
                    $rulesForField[] = 'array';
                    $rules[$field->input_name . '.*'] = Rule::in($allowedValues);
                } else {
                    $rulesForField[] = match ($field->type) {
                        'date' => 'date_format:Y-m-d',
                        'datetime-local' => 'date_format:Y-m-d\TH:i',
                        'email' => $field->type,
                        'number' => 'numeric',
                        'radio', 'select' => Rule::in($allowedValues),
                        default => 'string',
                    };
                }

                $rules[$field->input_name] = array_merge($rulesForField, $field->validation_rules ?? []);
            }
        }

        return array_replace($rules, $commonRules);
    }

    public function attributes()
    {
        $attributes = parent::attributes();

        foreach ($this->booking_option->form->formFieldGroups ?? [] as $group) {
            foreach ($group->formFields as $field) {
                $attributes[$field->input_name] = $field->name;
            }
        }

        return $attributes;
    }
}
