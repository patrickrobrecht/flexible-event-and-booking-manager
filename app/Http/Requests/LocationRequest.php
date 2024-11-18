<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesAddressFields;
use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property-read ?Location $location
 */
class LocationRequest extends FormRequest
{
    use ValidatesAddressFields;

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'nullable',
                'string',
                'max:255',
            ],
            'website_url' => [
                'nullable',
                'url:http,https',
                'max:255',
            ],
            ...$this->rulesForAddressFields('nullable'),
        ];
    }
}
