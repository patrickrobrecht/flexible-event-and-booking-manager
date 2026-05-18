<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\ApprovalStatus;
use App\Enums\DocumentReferenceType;
use App\Http\Controllers\DashboardController;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Document;
use App\Models\Event;
use App\Models\Location;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ApprovalStatus::class)]
#[CoversClass(Booking::class)]
#[CoversClass(BookingOption::class)]
#[CoversClass(Document::class)]
#[CoversClass(DocumentReferenceType::class)]
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

    public function testUserCanViewTheDashboardWithDocuments(): void
    {
        $user = $this->actingAsAnyUser();
        self::createDocument(static fn () => self::createEvent(), $user, [
            'approval_status' => ApprovalStatus::WaitingForApproval,
        ]);

        $documentStats = [
            '<span class="text-danger fw-bold">1</span>',
            '<span class="">0</span>',
            '<span class="">0</span>',
            '<span class="">0</span>',
        ];
        $this->get('/')
            ->assertOk()
            ->assertSeeHtmlInOrder([
                __('My documents'),
                ...$documentStats,
            ]);

        $this->actingAsUserWithAbility([Ability::ViewDocumentsOfEvents]);
        $this->get('/')
            ->assertOk()
            ->assertSeeHtmlInOrder([
                __('Documents'),
                ...$documentStats,
            ])
            ->assertDontSee(__('My documents'));
    }

    public function testUserCanViewTheDashboardWithMissingDocuments(): void
    {
        $user = $this->actingAsAnyUser();

        $event = self::createEvent();
        $eventSeries = self::createEventSeries();
        $organization = self::createOrganization();
        foreach ([$event, $eventSeries, $organization] as $object) {
            $object->saveResponsibleUsers([
                'responsible_user_id' => [$user->id],
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder([
                __('Missing documents'),
                $event->name,
                $eventSeries->name,
                $organization->name,
            ]);
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
