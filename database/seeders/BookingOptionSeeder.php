<?php

namespace Database\Seeders;

use App\Models\BookingOption;
use App\Models\Event;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Illuminate\Database\Seeder;
use Random\RandomException;

class BookingOptionSeeder extends Seeder
{
    use ResolvesSeederDependencies;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $events = $this->resolveDependency(Event::class, EventSeeder::class);

        // Select about 70% of events to have booking options
        $selectedEvents = $events->random((int) ($events->count() * 0.7));

        foreach ($selectedEvents as $event) {
            // Create 1-3 booking options for each selected event
            BookingOption::factory(random_int(1, 3))
                ->for($event)
                ->create();
        }
    }
}
