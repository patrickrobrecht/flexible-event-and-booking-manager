<?php

namespace App\Http\Requests;

use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Options\Ability;
use App\Policies\PersonalAccessTokenPolicy;
use Illuminate\Foundation\Http\FormRequest;

class PersonalAccessTokenRequest extends FormRequest
{
    /** {@see PersonalAccessTokenPolicy} via {@see PersonalAccessTokenController} */
    use AuthorizationViaController;

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
