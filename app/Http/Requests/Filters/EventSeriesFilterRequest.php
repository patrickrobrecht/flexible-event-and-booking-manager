<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\EventSeriesController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\EventSeries;
use App\Policies\EventSeriesPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see EventSeries}s
 */
class EventSeriesFilterRequest extends FormRequest
{
    /** {@see EventSeriesPolicy} in {@see EventSeriesController} */
    use AuthorizationViaController;
    use FiltersList;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'filter.name' => $this->ruleForText(),
        ];
    }
}
