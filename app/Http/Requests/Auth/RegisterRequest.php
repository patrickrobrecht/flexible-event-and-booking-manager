<?php

namespace App\Http\Requests\Auth;

use Carbon\Carbon;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    /**
     * @return array<string, list<Closure|Password|string>>
     */
    public function rules(): array
    {
        return [
            'start_time' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if ((int) Carbon::now()->timestamp - (int) $value < 5) {
                        // The error message is not displayed in the UI, so it does not need to be translated.
                        $fail('Bot detected.');
                    }
                },
            ],
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
                'regex:/^([0-9\s\ \+\(\)]*)$/',
            ],
            'fax' => [
                // The honeypot field must not be empty.
                'nullable',
                'string',
                'size:0',
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => [
                'required',
                'confirmed',
                Password::default(),
            ],
            'terms_and_conditions' => [
                config('app.urls.terms_and_conditions') ? 'accepted' : 'nullable',
            ],
        ];
    }
}
