<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\Traits\FiltersList;
use App\Models\StorageLocation;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Stringable;

/**
 * Filter for {@see StorageLocation}s.
 */
class StorageLocationFilterRequest extends FormRequest
{
    use FiltersList;

    /**
     * @return array<string, array<int, Closure|string|Stringable|ValidationRule>>
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
            'filter.description' => $this->ruleForText(),
            'sort' => [
                'nullable',
                StorageLocation::sortOptions()->getRule(),
            ],
        ];
    }
}
