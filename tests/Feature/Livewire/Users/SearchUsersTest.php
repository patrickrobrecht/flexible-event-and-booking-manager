<?php

namespace Tests\Feature\Livewire\Users;

use App\Livewire\Users\SearchUsers;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsAsUser;

#[CoversClass(SearchUsers::class)]
#[CoversClass(User::class)]
class SearchUsersTest extends TestCase
{
    use ActsAsUser;

    public function testComponentRendersCorrectly(): void
    {
        Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::empty()])
            ->assertStatus(200);
    }

    public function testUsersCanBeSearched(): void
    {
        $user1 = self::createUser(['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@example.com']);
        $user2 = self::createUser(['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane.smith@example.com']);
        self::createUser(['first_name' => 'Jake', 'last_name' => 'Doe', 'email' => 'jake.doe@example.com']);

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::empty()])
            ->set('searchTerm', 'John, jane.smith@example.com');
        $component->assertViewHas('users', Collection::make([$user1, $user2]));
    }

    public function testSelectedUsersAreNotInSearchResults(): void
    {
        $user = self::createUser(['first_name' => 'John', 'last_name' => 'Doe']);
        $selectedUser = self::createUser(['first_name' => 'Jake', 'last_name' => 'Doe']);

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::make([$selectedUser])])
            ->set('searchTerm', 'John, Jake');
        $component->assertViewHas('users', Collection::make([$user]));
    }

    public function testUsersCanBeSelected(): void
    {
        $user = self::createUser();

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::empty()])
            ->call('addUser', $user->id);
        self::assertEquals($component->get('selectedUsers')->pluck('id')->toArray(), [$user->id]);
    }

    public function testNonExistingUsersCannotBeSelected(): void
    {
        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::empty()])
            ->call('addUser', 999);
        $component->assertSet('selectedUsers', Collection::empty());
    }

    public function testSelectedUsersCanBeRemoved(): void
    {
        $selectedUser = self::createUser();

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::make([$selectedUser])])
            ->call('removeUser', $selectedUser->id);
        $component->assertSet('selectedUsers', Collection::empty());
    }

    public function testPositionAndSortOfOtherUsersArePreservedAfterAddingAUser(): void
    {
        $event = self::createEvent();
        $existingUser = self::createUser();
        self::attachResponsibleUser($event, $existingUser, 'Chairperson', 1);
        $userToAdd = self::createUser();

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => $event->responsibleUsers])
            ->call('addUser', $userToAdd->id);

        self::assertPositionAndSortArePreserved($component, $existingUser, 'Chairperson', 1);
    }

    public function testPositionAndSortOfOtherUsersArePreservedAfterRemovingAUser(): void
    {
        $event = self::createEvent();
        $remainingUser = self::createUser();
        self::attachResponsibleUser($event, $remainingUser, 'Chairperson', 1);
        $userToRemove = self::createUser();
        self::attachResponsibleUser($event, $userToRemove, 'Secretary', 2);

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => $event->responsibleUsers])
            ->call('removeUser', $userToRemove->id);

        self::assertPositionAndSortArePreserved($component, $remainingUser, 'Chairperson', 1);
    }

    private static function attachResponsibleUser(Event $event, User $user, string $position, int $sort): void
    {
        $event->responsibleUsers()->attach($user->id, [
            'publicly_visible' => true,
            'position' => $position,
            'sort' => $sort,
        ]);
    }

    /**
     * @param Testable<SearchUsers> $component
     */
    private static function assertPositionAndSortArePreserved(Testable $component, User $user, string $position, int $sort): void
    {
        self::assertSame($position, $component->get('selectedUserData')[$user->id]['position'] ?? null);
        self::assertSame($sort, $component->get('selectedUserData')[$user->id]['sort'] ?? null);
        $component->assertSeeHtml('value="' . $position . '"');
    }
}
