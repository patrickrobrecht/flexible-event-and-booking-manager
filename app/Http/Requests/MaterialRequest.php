<?php

namespace App\Http\Requests;

use App\Models\Material;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Stringable;

/**
 * @property-read ?Material $material
 */
class MaterialRequest extends FormRequest
{
    /**
     * @return array<string, array<int, string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'organization_id' => [
                'required',
                Rule::exists('organizations', 'id'),
            ],
        ];
    }
}
