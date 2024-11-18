<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesAddressFields;
use App\Models\User;
use App\Options\ActiveStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * @property-read ?User $user
 */
class UserRequest extends FormRequest
{
    use ValidatesAddressFields;

    protected function prepareForValidation(): void
    {
        $this->merge([
            'user_role_id' => $this->input('user_role_id', []), // Force array!
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $uniqueRule = Rule::unique('users');
        if ($this->routeIs('account.update')) {
            $uniqueRule->ignore(Auth::id());
        } elseif ($this->routeIs('users.update')) {
            $uniqueRule->ignore($this->user);
        }

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
                $uniqueRule,
            ],
            'password' => [
                'nullable',
                'confirmed',
                Password::defaults(),
            ],
            'send_notification' => [
                $this->routeIs('users.store') ? 'nullable' : 'prohibited',
                'boolean',
            ],
        ];

        $rules = array_replace($rules, $this->rulesForAddressFields('nullable'));

        if ($this->routeIs('users.store', 'users.update')) {
            $rules = array_replace($rules, [
                'status' => [
                    'required',
                    ActiveStatus::rule(),
                ],
                'user_role_id' => [
                    'sometimes',
                    'array',
                ],
                'user_role_id.*' => [
                    Rule::exists('user_roles', 'id'),
                ],
            ]);
        }

        return $rules;
    }
}
