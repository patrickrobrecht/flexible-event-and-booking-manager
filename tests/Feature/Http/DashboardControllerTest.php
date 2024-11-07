<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\DashboardController;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use Database\Factories\BookingFactory;
use Database\Factories\BookingOptionFactory;
use Database\Factories\EventFactory;
use Database\Factories\LocationFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DashboardController::class)]
#[CoversClass(Booking::class)]
#[CoversClass(BookingFactory::class)]
#[CoversClass(BookingOption::class)]
#[CoversClass(BookingOptionFactory::class)]
#[CoversClass(Event::class)]
#[CoversClass(EventFactory::class)]
#[CoversClass(Location::class)]
#[CoversClass(LocationFactory::class)]
class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testTheDashboardIsAccessibleByEveryone(): void
    {
        $this->assertRouteAccessibleAsGuest('/');
    }

    public function testTheDashboardShowsOwnBookingsIfLoggedIn(): void
    {
        $user = $this->actingAsAnyUser();
        $eventsCount = fake()->numberBetween(1, 5);
        $events = $this->createEvents($user, $eventsCount);
        $this->assertCount($eventsCount, $user->bookings);

        $response = $this->get('/')
            ->assertOk()
            ->assertSee('fa-file-contract');
        $events->each(fn ($event) => $response->assertSee($event->name));
    }

    public function testTheDashboardDoesNotShowBookingsAsGuest(): void
    {
        $this->createEvents(User::factory()->create(), 5);
        $this->get('/')
            ->assertOk()
            ->assertDontSee('fa-file-contract');
    }

    private function createEvents(User $bookedByUser, int $eventsCount): Collection
    {
        return Event::factory()
            ->for(Location::factory()->create())
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
