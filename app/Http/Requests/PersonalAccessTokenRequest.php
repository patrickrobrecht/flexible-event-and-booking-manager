<?php

namespace App\Http\Requests;

use App\Options\Ability;
use Illuminate\Foundation\Http\FormRequest;

class PersonalAccessTokenRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'abilities' => $this->input('abilities', []), // Force array!
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
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
