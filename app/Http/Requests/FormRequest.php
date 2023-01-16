<?php

namespace App\Http\Requests;

use App\Http\Controllers\FormController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Policies\FormPolicy;
use Illuminate\Foundation\Http\FormRequest as LaravelFormRequest;

class FormRequest extends LaravelFormRequest
{
    /** {@see FormPolicy} in {@see FormController} */
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
        ];
    }
}
