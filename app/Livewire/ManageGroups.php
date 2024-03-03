<?php

namespace App\Livewire;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Component;

class ManageGroups extends Component
{
    public Event $event;

    /** @var Collection<Group> */
    public Collection $groups;

    /** @var Collection<Booking> */
    public Collection $bookingsWithoutGroup;

    public string $sort = 'name';

    public function mount(Event $event): void
    {
        $this->event = $event;

        $this->loadData();
    }

    private function loadData(): void
    {
        $this->event->load([
            'bookings.groups',
            'groups',
        ]);

        $this->groups = $this->event->groups->keyBy('id');

        $this->bookingsWithoutGroup = $this->event->bookings->filter(
            fn (Booking $booking) => $booking->getGroup($this->event) === null
        );
    }

    public function createGroup()
    {
    }

    public function editGroup($groupId)
    {
    }

    public function deleteGroup($groupId): void
    {
        /** @var Group $group */
        $group = $this->groups[$groupId] ?? null;
        if ($group) {
            $this->authorize('forceDelete', $group);
            $this->groups->forget($groupId);
            $group->delete();
            Session::flash('success', __('Group :name deleted successfully.', [
                'name' => $group->name,
            ]));
        }
    }

    public function moveBooking($bookingId, $groupId): void
    {
        $groupId = (int) $groupId;

        /** @var Booking $booking */
        $booking = Booking::query()->find($bookingId);
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

        return view('livewire.manage-groups', [
            'bookingsWithoutGroup' => $this->bookingsWithoutGroup,
            'groups' => $this->groups,
        ]);
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
