<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserRoleSeeder::class,
            UserSeeder::class, // needs user roles

            LocationSeeder::class,
            OrganizationSeeder::class, // needs locations and users
            EventSeeder::class, // needs locations, organizations and users
            EventSeriesSeeder::class, // needs  organizations and users
            BookingOptionSeeder::class, // needs events
            BookingSeeder::class, // needs booking options and users

            StorageLocationSeeder::class,
            MaterialSeeder::class, // needs organizations and storage locations
        ]);
    }
}
