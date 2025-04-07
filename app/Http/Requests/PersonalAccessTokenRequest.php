<?php

namespace App\Http\Requests;

use App\Enums\Ability;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Stringable;

class PersonalAccessTokenRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'abilities' => $this->input('abilities', []), // Force array!
        ]);
    }

    /**
     * @return array<string, array<string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'expires_at' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],
            'abilities' => [
                'sometimes',
                'array',
            ],
            'abilities.*' => [
                Ability::rule(),
            ],
        ];
    }
}
