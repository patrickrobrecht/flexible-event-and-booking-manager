<?php

namespace App\Http\Requests;

use App\Models\Location;
use App\Models\StorageLocation;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
        $exists = Rule::exists('storage_locations', 'id');
        if (isset($this->storage_location)) {
            $exists->whereNotIn('id', Collection::make($this->storage_location->getDescendantsAndSelf())->pluck('id'));
        }

        return [
            'name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('storage_locations', 'name')
                    ->ignore($this->storage_location ?? null),
            ],
            'description' => [
                'nullable',
                'string',
            ],
            'packaging_instructions' => [
                'nullable',
                'string',
            ],
            'parent_storage_location_id' => [
                'nullable',
                $exists,
            ],
        ];
    }
}
