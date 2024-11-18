<?php

namespace App\Http\Requests\Filters;

use App\Http\Requests\Traits\FiltersList;
use App\Models\Booking;
use App\Models\Group;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Filter for {@see Group}s
 */
class GroupFilterRequest extends FormRequest
{
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
