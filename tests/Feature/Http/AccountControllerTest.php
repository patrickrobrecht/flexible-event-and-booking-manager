<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\ApprovalStatus;
use App\Http\Controllers\AccountController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Policies\UserPolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $this->assertUserCanGetOnlyWithAbility('/account', Ability::ViewAccount);
    }

    public function testUserCanViewAbilitiesOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account/abilities', Ability::ViewAbilities);
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

    public function testUserCanViewEditAccountFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account/edit', Ability::EditAccount);
    }

    public function testUserCanUpdateAccountWithCorrectAbility(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::EditAccount);
        $userData = $this->getRandomUserData($user);

        $this->put('/account', $userData)
            ->assertRedirect('/account/edit')
            ->assertSessionHasNoErrors();
        $user->refresh();
        self::assertEquals($userData['first_name'], $user->first_name);
        self::assertEquals($userData['last_name'], $user->last_name);
    }

    public function testUserCannotUpdateAccountWithoutAbility(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::ViewAccount);

        $this->put('/account', $this->getRandomUserData($user))
            ->assertForbidden();
    }

    /**
     * @param array<string, mixed> $changedData
     */
    #[DataProvider('sensitiveAccountChanges')]
    public function testCurrentPasswordIsRequiredForSensitiveAccountChanges(array $changedData, ?string $currentPassword, ?string $expectedError): void
    {
        $user = $this->actingAsUserWithAbility(Ability::EditAccount);

        $data = array_replace($this->getRandomUserData($user), $changedData);
        if ($currentPassword !== null) {
            $data['current_password'] = $currentPassword;
        }

        $response = $this->put('/account', $data);

        if ($expectedError !== null) {
            $response->assertSessionHasErrors(['current_password' => $expectedError]);
        } else {
            $response->assertSessionHasNoErrors()->assertRedirect('/account/edit');
        }
    }

    /**
     * @return list<array{array<string, mixed>, ?string, ?string}>
     */
    public static function sensitiveAccountChanges(): array
    {
        $changedEmail = ['email' => 'new-address@example.com'];
        $changedPassword = ['password' => 'new-password', 'password_confirmation' => 'new-password'];

        return [
            // Changed email address.
            [$changedEmail, null, 'Derzeitiges Passwort muss ausgefüllt werden.'],
            [$changedEmail, 'wrong-password', 'Das Passwort ist falsch.'],
            [$changedEmail, 'password', null],

            // Changed password.
            [$changedPassword, null, 'Derzeitiges Passwort muss ausgefüllt werden, wenn Passwort ausgefüllt wurde.'],
            [$changedPassword, 'wrong-password', 'Das Passwort ist falsch.'],
            [$changedPassword, 'password', null],
        ];
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
            'current_password' => '', // Always sent from the UI.
        ];
    }
}
