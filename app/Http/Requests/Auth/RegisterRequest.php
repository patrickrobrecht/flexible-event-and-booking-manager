<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\Traits\AuthorizationViaController;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    use AuthorizationViaController;

    public function rules(): array
    {
        return [
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
                Password::defaults(),
            ],
            'terms_and_conditions' => [
                config('app.urls.terms_and_conditions') ? 'accepted' : 'nullable',
            ],
        ];
    }
}
