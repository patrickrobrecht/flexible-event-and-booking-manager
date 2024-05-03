<?php

namespace App\Http\Requests\Traits;

use Illuminate\Validation\Rule;

trait ValidatesResponsibleUsers
{
    protected function rulesForResponsibleUsers()
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
        ];
    }
}
