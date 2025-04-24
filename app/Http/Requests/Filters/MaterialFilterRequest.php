<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\Traits\FiltersList;
use App\Models\Material;
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
        ];
    }
}
