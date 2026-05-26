<?php

namespace App\Http\Requests\Traits;

trait ValidatesAddressFields
{
    /**
     * @return array<string, string[]>
     */
    protected function rulesForAddressFields(string $default): array
    {
        return [
            'street' => [
                $default,
                'required_with:house_number',
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
                'required_with:postal_code',
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
