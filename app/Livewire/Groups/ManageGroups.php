<?php

namespace App\Livewire\Groups;

use App\Livewire\Forms\GroupForm;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageGroups extends Component
{
    public Event $event;

    /** @var Collection<Group> */
    public Collection $groups;

    /** @var Collection<Booking> */
    public Collection $bookingsWithoutGroup;

    public string $sort = 'name';

    public GroupForm $form;

    public function mount(Event $event): void
    {
        $this->event = $event;

        $this->loadData();
    }

    private function loadData(): void
    {
        $this->event->load([
            'bookings.bookingOption',
            'bookings.groups',
            'groups',
        ]);

        $this->groups = $this->event->groups->keyBy('id');

        $this->bookingsWithoutGroup = $this->event->bookings->filter(
            fn (Booking $booking) => $booking->getGroup($this->event) === null
        );
    }

    /**
     * Handle create form submission.
     */
    public function createGroup(): void
    {
        $this->authorize('create', Group::class);

        $validated = $this->form->validateGroupForEvent($this->event, null);

        $group = new Group();
        $group->event()->associate($this->event);
        if ($group->fill($validated)->save()) {
            $this->updateGroupInList($group);

            Session::flash('success', __('Group :name created successfully.', [
                'name' => $group->name,
            ]));

            $this->form->reset();
        }
    }

    private function updateGroupInList(Group $group): void
    {
        $this->groups[$group->id] = $group;
        $this->groups = $this->groups->sortBy('name');
    }

    #[On('group-updated')]
    public function updateGroup(Group $group): void
    {
        $this->updateGroupInList($group);

        Session::flash('success', __('Group :name updated successfully.', [
            'name' => $group->name,
        ]));
    }

    public function deleteGroup($groupId): void
    {
        $group = $this->getGroupById($groupId);
        if ($group) {
            $this->authorize('forceDelete', $group);
            $this->groups->forget($groupId);
            $group->delete();
            Session::flash('success', __('Group :name deleted successfully.', [
                'name' => $group->name,
            ]));
        }
    }

    public function getGroupById($groupId): ?Group
    {
        return $this->groups[$groupId] ?? null;
    }

    public function moveBooking($bookingId, $groupId): void
    {
        $groupId = (int) $groupId;

        /** @var Booking $booking */
        $booking = Booking::query()->find((int) $bookingId);
        if ($booking) {
            $oldGroup = $booking->getGroup($this->event);
            if (isset($oldGroup)) {
                if ($oldGroup->id === $groupId) {
                    // Already correctly attached.
                    return;
                }

                $booking->groups()->detach($oldGroup);
            }

            if ($groupId !== -1) {
                $booking->groups()->attach($groupId);
            }

            $this->loadData();
        }
    }

    public function render(): View
    {
        $this->sortBookings();

        return view('livewire.manage-groups');
    }

    /**
     * React on changes of $this->sort.
     */
    public function updatedSort(): void
    {
        $this->sortBookings();
    }

    private function sortBookings(): void
    {
        $this->groups = $this->groups
            ->map(function (Group $group) {
                $group['bookings'] = $this->sortBookingsInGroup(
                    $this->event->bookings->filter(
                        fn (Booking $booking) => $booking->getGroup($this->event)?->is($group)
                    )
                );

                return $group;
            });

        $this->bookingsWithoutGroup = $this->sortBookingsInGroup($this->bookingsWithoutGroup);
    }

    /**
     * @param Collection<Booking> $bookings
     * @return Collection<Booking>
     */
    private function sortBookingsInGroup(Collection $bookings): Collection
    {
        $column = ltrim($this->sort, '-');
        if ($column === 'name') {
            $column = fn (Booking $booking) => $booking->last_name . ', ' . $booking->first_name;
        }

        return $bookings->sortBy($column, descending: str_starts_with($this->sort, '-'));
    }
}