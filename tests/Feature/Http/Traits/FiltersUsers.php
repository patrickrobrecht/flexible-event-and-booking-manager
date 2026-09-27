<?php

namespace Tests\Feature\Http\Traits;

trait FiltersUsers
{
    /**
     * @return array<string, array<string, string>>
     */
    public static function exampleUserData(): array
    {
        return [
            'john' => [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '123456789',
                'email' => 'john@example.com',
                'postal_code' => '01234',
            ],
            'jack' => [
                'first_name' => 'Jack',
                'last_name' => 'Doe',
                'phone' => '123555555',
                'email' => 'jack@example.com',
                'postal_code' => '56789',
            ],
            'jane' => [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '987654321',
                'email' => 'jane@test.com',
                'postal_code' => '56123',
            ],
        ];
    }

    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function userFilters(): array
    {
        return [
            'exact first name' => [
                'filter[name]=Jane',
                ['jane'],
            ],
            'exact last name' => [
                'filter[name]=Doe',
                ['john', 'jack'],
            ],
            'partial first name' => [
                'filter[name]=Joh',
                ['john'],
            ],

            'partial phone' => [
                'filter[phone]=123',
                ['john', 'jack'],
            ],
            'exact phone' => [
                'filter[phone]=987654321',
                ['jane'],
            ],

            'partial email' => [
                'filter[email]=example.com',
                ['john', 'jack'],
            ],
            'exact email' => [
                'filter[email]=jane@test.com',
                ['jane'],
            ],

            'postal code' => [
                'filter[postal_code]=56789',
                ['jack'],
            ],
            'exclude postal code' => [
                'filter[postal_code]=-56789',
                ['john', 'jane'],
            ],
            'multiple postal codes' => [
                'filter[postal_code]=01234,56789',
                ['john', 'jack'],
            ],
            'exclude multiple postal codes' => [
                'filter[postal_code]=-01234,-56789',
                ['jane'],
            ],
        ];
    }
}
