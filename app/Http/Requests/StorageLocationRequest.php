<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\StorageLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Stringable;

/**
 * @property-read ?StorageLocation $storage_location
 */
class StorageLocationRequest extends FormRequest
{
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
            'description' => [
                'nullable',
                'string',
            ],
            'packaging_instructions' => [
                'nullable',
                'string',
            ],
        ];
    }
}
