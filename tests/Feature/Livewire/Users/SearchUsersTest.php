<?php

namespace Tests\Feature\Livewire\Users;

use App\Livewire\Users\SearchUsers;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
        $user1 = User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe', 'email' => 'john.doe@example.com']);
        $user2 = User::factory()->create(['first_name' => 'Jane', 'last_name' => 'Smith', 'email' => 'jane.smith@example.com']);
        User::factory()->create(['first_name' => 'Jake', 'last_name' => 'Doe', 'email' => 'jake.doe@example.com']);

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::empty()])
            ->set('searchTerm', 'John, jane.smith@example.com');
        $component->assertViewHas('users', Collection::make([$user1, $user2]));
    }

    public function testSelectedUsersAreNotInSearchResults(): void
    {
        $user = User::factory()->create(['first_name' => 'John', 'last_name' => 'Doe']);
        $selectedUser = User::factory()->create(['first_name' => 'Jake', 'last_name' => 'Doe']);

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::make([$selectedUser])])
            ->set('searchTerm', 'John, Jake');
        $component->assertViewHas('users', Collection::make([$user]));
    }

    public function testUsersCanBeSelected(): void
    {
        $user = User::factory()->create();

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
        $selectedUser = User::factory()->create();

        $component = Livewire::test(SearchUsers::class, ['selectedUsers' => Collection::make([$selectedUser])])
            ->call('removeUser', $selectedUser->id);
        $component->assertSet('selectedUsers', Collection::empty());
    }
}
