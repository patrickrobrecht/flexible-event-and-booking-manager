<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\DashboardController;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Booking::class)]
#[CoversClass(BookingOption::class)]
#[CoversClass(DashboardController::class)]
#[CoversClass(Event::class)]
#[CoversClass(Location::class)]
class DashboardControllerTest extends TestCase
{
    public function testGuestCanViewTheDashboard(): void
    {
        $this->createEvents(User::factory()->create(), 5);
        $this->get('/')
            ->assertOk()
            ->assertDontSee('fa-file-contract'); // no booking
    }

    public function testUserCanViewTheDashboardWithOwnBookings(): void
    {
        $user = $this->actingAsAnyUser();
        $eventsCount = fake()->numberBetween(1, 5);
        $events = $this->createEvents($user, $eventsCount);
        self::assertCount($eventsCount, $user->bookings);

        $response = $this->get('/')
            ->assertOk()
            ->assertSee('fa-file-contract');
        $events->each(fn (Event $event) => $response->assertSee($event->name));
    }

    /**
     * @return Collection<int, Event>
     */
    private function createEvents(User $bookedByUser, int $eventsCount): Collection
    {
        return Event::factory()
            ->for(self::createLocation())
            ->for(self::createOrganization())
            ->has(
                BookingOption::factory()
                    ->has(
                        Booking::factory()
                            ->for($bookedByUser, 'bookedByUser')
                    )
            )
            ->count($eventsCount)
            ->create();
    }
}
