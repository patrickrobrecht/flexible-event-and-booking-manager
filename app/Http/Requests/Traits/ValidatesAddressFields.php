<?php

namespace App\Http\Requests\Traits;

trait ValidatesAddressFields
{
    protected function rulesForAddressFields(): array
    {
        return [
            'street' => [
                'nullable',
                'string',
                'max:255',
            ],
            'house_number' => [
                'nullable',
                'string',
                'alpha_num',
                'max:255'
            ],
            'postal_code' => [
                'nullable',
                'string',
                'alpha_num',
                'max:255'
            ],
            'city' => [
                'nullable',
                'string',
                'max:255',
            ],
            'country' => [
                'nullable',
                'string',
                'max:255',
            ],
        ];
    }
}
