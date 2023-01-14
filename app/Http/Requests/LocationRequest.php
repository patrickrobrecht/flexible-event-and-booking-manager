<?php

namespace App\Http\Requests;

use App\Http\Controllers\LocationController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\ValidatesAddressFields;
use App\Models\Location;
use App\Policies\LocationPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read ?Location $location
 */
class LocationRequest extends FormRequest
{
    /** {@see LocationPolicy} in {@see LocationController} */
    use AuthorizationViaController;
    use ValidatesAddressFields;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'website_url' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];

        return array_replace($rules, $this->rulesForAddressFields('nullable'));
    }
}
