<?php

namespace App\Http\Requests\Filters;

use App\Enums\FilterValue;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Material;
use App\Models\StorageLocation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Stringable;

/**
 * Filter for {@see Material}s
 */
class MaterialFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * @return array<string, array<int, Closure|ValidationRule|string|Stringable>>
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.description' => $this->ruleForText(),
            'filter.organization_id' => $this->ruleForForeignId('organizations'),
            'filter.storage_location_id' => $this->ruleForAllowedOrExistsInDatabase(StorageLocation::query(), FilterValue::values()),
        ];
    }
}
