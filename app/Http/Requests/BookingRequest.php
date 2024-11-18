<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesAddressFields;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Options\FormElementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property-read BookingOption $booking_option
 * @property-read ?Booking $booking
 */
class BookingRequest extends FormRequest
{
    use ValidatesAddressFields;

    public function prepareForValidation(): void
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
            'date_of_birth' => [
                'nullable',
                'date_format:Y-m-d',
                'after:1900-01-01',
            ],
            'phone' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^([0-9\s\ \+\(\)]*)$/',
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

        if ($this->booking_option->formFields->isEmpty()) {
            return array_replace($defaultRules, $commonRules, $this->rulesForAddressFields('nullable'));
        }

        $rules = [];
        foreach ($this->booking_option->formFields as $field) {
            if ($field->type->isStatic()) {
                continue;
            }

            $isFileWithExistingUpload = $field->type === FormElementType::File
                && isset($this->booking)
                && $this->booking->getFieldValue($field) !== null;

            $rulesForField = [
                $field->required && !$isFileWithExistingUpload ? 'required' : 'nullable',
            ];
            $allowedValues = $field->allowed_values ?? [];

            if ($field->type === FormElementType::Checkbox && count($allowedValues) > 1) {
                $rulesForField[] = 'array';
                $rules[$field->input_name . '.*'] = Rule::in($allowedValues);
            } else {
                $rulesForField[] = match ($field->type) {
                    FormElementType::Date => 'date_format:Y-m-d',
                    FormElementType::DateTime => 'date_format:Y-m-d\TH:i',
                    FormElementType::Email => 'email',
                    FormElementType::File => 'file',
                    FormElementType::Number => 'numeric',
                    FormElementType::Radio, FormElementType::Select => Rule::in($allowedValues),
                    default => 'string',
                };
            }

            $rules[$field->input_name] = array_merge($rulesForField, $field->validation_rules ?? []);
        }

        return array_replace($rules, $commonRules);
    }

    public function attributes()
    {
        $attributes = parent::attributes();

        foreach ($this->booking_option->formFields ?? [] as $field) {
            $attributes[$field->input_name] = $field->name;
        }

        return $attributes;
    }
}
