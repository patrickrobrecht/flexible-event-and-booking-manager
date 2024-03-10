<?php

namespace App\Http\Requests\Filters;

use App\Http\Controllers\GroupController;
use App\Http\Requests\Traits\AuthorizationViaController;
use App\Http\Requests\Traits\FiltersList;
use App\Models\Booking;
use App\Models\Group;
use App\Policies\GroupPolicy;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Group}s
 */
class GroupFilterRequest extends FormRequest
{
    /** {@see GroupPolicy} in {@see GroupController} */
    use AuthorizationViaController;
    use FiltersList;

    public function rules(): array
    {
        return [
            'sort' => [
                'nullable',
                Booking::sortOptions()->getRule(),
            ],
        ];
    }
}
