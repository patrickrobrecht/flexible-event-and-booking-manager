<?php

namespace App\Http\Requests;

use App\Enums\MaterialStatus;
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
    protected function prepareForValidation(): void
    {
        /** @var array<string, array<string, string>> $storageLocations */
        $storageLocations = $this->input('storage_locations', []);

        foreach ($storageLocations as $key => &$location) {
            if (isset($location['remove'])) {
                $location['remove'] = filter_var($location['remove'], FILTER_VALIDATE_BOOLEAN);
            }
        }
        unset($location);

        $this->merge([
            'storage_locations' => $storageLocations,
        ]);
    }

    /**
     * @return array<string, array<int, string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('materials', 'name')
                    ->ignore($this->material ?? null),
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

        if (!isset($this->material)) {
            return $rules;
        }

        $rules['storage_locations'] = [
            'nullable',
            'array',
        ];
        $rules['storage_locations.new.storage_location_id'] = [
            'nullable',
            Rule::exists('storage_locations', 'id'),
        ];
        $rules['storage_locations.new.material_status'] = [
            'nullable',
            'required_with:storage_locations.new.storage_location_id',
            Rule::prohibitedIf(fn () => is_null($this->input('storage_locations.new.storage_location_id'))),
            MaterialStatus::rule(),
        ];
        $rules['storage_locations.new.stock'] = [
            'nullable',
            Rule::prohibitedIf(fn () => is_null($this->input('storage_locations.new.storage_location_id'))),
            'integer',
            'gte:0',
        ];
        $rules['storage_locations.new.remarks'] = [
            'nullable',
            Rule::prohibitedIf(fn () => is_null($this->input('storage_locations.new.storage_location_id'))),
            'string',
        ];

        foreach (array_diff($this->getStorageLocationIds(), ['new']) as $id) {
            /** @var string $id */
            $prefix = 'storage_locations.' . $id . '.';
            $rules[$prefix . 'storage_location_id'] = [
                'required',
                Rule::exists('storage_locations', 'id'),
            ];
            $rules[$prefix . 'remove'] = [
                'nullable',
                'boolean',
            ];
            $rules[$prefix . 'material_status'] = [
                'required',
                MaterialStatus::rule(),
            ];
            $rules[$prefix . 'stock'] = [
                'nullable',
                'integer',
                'gte:0',
            ];
            $rules[$prefix . 'remarks'] = [
                'nullable',
                'string',
            ];
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        if (!isset($this->material)) {
            return [];
        }

        $attributes = [];
        foreach ($this->getStorageLocationIds() as $id) {
            $prefix = 'storage_locations.' . $id . '.';
            $attributes[$prefix . 'storage_location_id'] = __('Storage location');
            $attributes[$prefix . 'material_status'] = __('Status');
            $attributes[$prefix . 'stock'] = __('Stock');
            $attributes[$prefix . 'remarks'] = __('Remarks');
        }

        return $attributes;
    }

    /**
     * @return array<int, int|string>
     */
    private function getStorageLocationIds(): array
    {
        /** @phpstan-ignore argument.type */
        return array_keys($this->input('storage_locations', []));
    }
}
