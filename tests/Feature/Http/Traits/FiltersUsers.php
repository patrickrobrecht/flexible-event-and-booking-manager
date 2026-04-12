<?php

namespace Tests\Feature\Http\Traits;

trait FiltersUsers
{
    /**
     * @return list<array<string, string>>
     */
    public static function exampleUserData(): array
    {
        return [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'phone' => '123456789',
                'email' => 'john@example.com',
                'postal_code' => '01234',
            ],
            [
                'first_name' => 'Jack',
                'last_name' => 'Doe',
                'phone' => '123555555',
                'email' => 'jack@example.com',
                'postal_code' => '56789',
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'phone' => '987654321',
                'email' => 'jane@test.com',
                'postal_code' => '56123',
            ],
        ];
    }

    /**
     * @return list<array{string, list<string>|string, list<string>|string}>
     */
    public static function userFilters(): array
    {
        return [
            ['filter[name]=Jane', ['Jane Smith'], ['John Doe', 'Jack Doe']], // exact first name
            ['filter[name]=Doe', ['John Doe', 'Jack Doe'], ['Jane Smith']], // exact last name
            ['filter[name]=Joh', ['John Doe'], ['Jane Smith', 'Jack Doe']], // partial first name

            ['filter[phone]=123', ['John Doe', 'Jack Doe'], ['Jane']], // partial phone
            ['filter[phone]=987654321', ['Jane Smith'], ['John Doe', 'Jack Doe']], // exact phone

            ['filter[email]=example.com', ['John Doe', 'Jack Doe'], ['Jane Smith']], // partial email
            ['filter[email]=jane@test.com', ['Jane Smith'], ['John Doe', 'Jack Doe']], // exact email

            ['filter[postal_code]=56789', ['Jack Doe'], ['John Doe', 'Jane Smith']], // postal code
            ['filter[postal_code]=-56789', ['John Doe', 'Jane Smith'], ['Jack Doe']], // exclude postal code
        ];
    }
}
