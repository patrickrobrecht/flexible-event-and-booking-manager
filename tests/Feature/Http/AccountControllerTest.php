<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\ApprovalStatus;
use App\Http\Controllers\AccountController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Policies\UserPolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ApprovalStatus::class)]
#[CoversClass(AccountController::class)]
#[CoversClass(User::class)]
#[CoversClass(UserPolicy::class)]
#[CoversClass(UserRequest::class)]
class AccountControllerTest extends TestCase
{
    public function testUserCanViewAccountOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account', [Ability::ViewAccount, Ability::ViewAbilities]);
    }

    public function testUserCanViewAccountWithMissingDocuments(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::ViewAccount);

        $event = self::createEvent();
        $eventSeries = self::createEventSeries();
        $organization = self::createOrganization();
        foreach ([$event, $eventSeries, $organization] as $object) {
            $object->saveResponsibleUsers([
                'responsible_user_id' => [$user->id],
            ]);
        }

        $this->get('/account')
            ->assertOk()
            ->assertSeeInOrder([
                __('Missing documents'),
                $event->name,
                $eventSeries->name,
                $organization->name,
            ]);
    }

    public function testUserCanViewAbilitiesOnlyWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewAccount);
        $this->get('/account')->assertDontSee(__('Abilities'));

        $this->actingAsUserWithAbility([Ability::ViewAccount, Ability::ViewAbilities]);
        $this->get('/account')->assertSee(__('Abilities'));
    }

    public function testUserCanViewOwnBookings(): void
    {
        $user = $this->actingAsAnyUser();
        $noBookingsMessage = __('You do not have any bookings yet.');
        $this->get('/account/bookings')
            ->assertSee($noBookingsMessage);

        $bookingOption = self::createBookingOptionForEvent();
        self::createBookingsForUser($bookingOption, $user);
        $this->get('/account/bookings')
            ->assertDontSee($noBookingsMessage)
            ->assertSee($bookingOption->event->name);
    }

    public function testUserCanViewOwnDocuments(): void
    {
        $user = $this->actingAsAnyUser();
        $document = self::createDocument(static fn () => self::createEvent(), uploadedByUser: $user);
        $documentUploadedByAnotherUser = self::createDocument(static fn () => self::createEvent()); // uploaded by another user

        $this->get('/account/documents')
            ->assertSee($document->title)
            ->assertDontSee($documentUploadedByAnotherUser->title);
    }

    public function testUserCannotUpdateAccountWithoutAbility(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::ViewAccount);

        $this->put('/account', $this->getRandomUserData($user))->assertForbidden();
    }

    public function testUserReceivesErrorMessagesForInvalidAccountData(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::EditAccount);

        $data = array_replace($this->getRandomUserData($user), ['first_name' => null]);
        $this->put('/account', $data)
            ->assertSessionHasErrors([
                'first_name' => 'Vorname muss ausgefüllt werden.',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getRandomUserData(User $user): array
    {
        $userData = User::factory()->makeOne();

        return [
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'email' => $user->email,
        ];
    }
}
