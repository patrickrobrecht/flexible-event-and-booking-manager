<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SearchUsers extends Component
{
    public $searchTerm = '';

    #[Locked]
    public $fieldName = 'user_id';

    /**
     * @var Collection<User>
     */
    #[Locked]
    public $selectedUsers;

    public function mount($selectedUsers): void
    {
        // Key selected users by their ID.
        $this->selectedUsers = $selectedUsers->keyBy('id');
    }

    public function render(): View
    {
        $searchTerms = array_filter(array_map('trim', explode(',', $this->searchTerm)));

        [$users, $usersCount] = count($searchTerms) === 0
            ? [Collection::empty(), 0]
            : [$this->userQuery($searchTerms)->limit(100)->get(), $this->userQuery($searchTerms)->count()];

        return view('livewire.users.search-users', [
            'users' => $users,
            'usersCount' => $usersCount,
        ]);
    }

    private function userQuery(array $searchTerms): Builder
    {
        return User::query()
            ->where(
                fn (Builder $query) => $query
                    /** @see User::scopeName() */
                    ->name(...$searchTerms)
                    /** @see User::scopeEmail() */
                    ->orWhere(fn (Builder $query2) => $query2->email(...$searchTerms))
            )
            ->whereNotIn('id', $this->selectedUsers->keys())
            ->orderBy('last_name')
            ->orderBy('first_name');
    }

    public function addUser($userId): void
    {
        $user = User::find($userId);
        if (isset($user)) {
            /** @phpstan-ignore-next-line */
            $this->selectedUsers[$userId] = $user;
        }
    }

    public function removeUser($userId): void
    {
        unset($this->selectedUsers[$userId]);
    }
}
