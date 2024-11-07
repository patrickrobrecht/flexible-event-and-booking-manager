<?php

namespace App\Http\Requests\Traits;

trait ValidatesAddressFields
{
    protected function rulesForAddressFields(string $default): array
    {
        return [
            'street' => [
                $default,
                'string',
                'max:255',
            ],
            'house_number' => [
                $default,
                'string',
                'regex:/^\d+[a-zA-Z]?\/?\d*$/',
                'max:255',
            ],
            'postal_code' => [
                $default,
                'string',
                'alpha_num',
                'max:255',
            ],
            'city' => [
                $default,
                'string',
                'max:255',
            ],
            'country' => [
                $default,
                'string',
                'max:255',
            ],
        ];
    }
}
