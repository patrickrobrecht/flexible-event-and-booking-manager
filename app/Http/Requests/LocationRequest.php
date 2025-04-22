<?php

namespace App\Http\Requests;

use App\Http\Requests\Traits\ValidatesAddressFields;
use App\Models\Location;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Stringable;

/**
 * @property-read ?Location $location
 */
class LocationRequest extends FormRequest
{
    use ValidatesAddressFields;

    /**
     * @return array<string, array<int, string|Stringable|ValidationRule>>
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
