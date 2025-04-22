<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;
use Stringable;

trait ValidatesResponsibleUsers
{
    /**
     * @return array<string, array<int, string|Stringable>>
     */
    protected function rulesForResponsibleUsers(): array
    {
        return [
            'responsible_user_id' => [
                'nullable',
                'array',
                'distinct:strict',
            ],
            'responsible_user_id.*' => [
                Rule::exists('users', 'id'),
            ],
            'responsible_user_data.*.publicly_visible' => [
                'nullable',
                'boolean',
            ],
            'responsible_user_data.*.position' => [
                'nullable',
                'string',
                'max:255',
            ],
            'responsible_user_data.*.sort' => [
                'nullable',
                'integer',
                'min:1',
                'max:999999',
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function attributesForResponsibleUsers(): array
    {
        return [
            'responsible_user_data.*.publicly_visible' => __('publicly visible'),
            'responsible_user_data.*.position' => __('Position'),
            'responsible_user_data.*.sort' => __('Sort'),
        ];
    }
}
