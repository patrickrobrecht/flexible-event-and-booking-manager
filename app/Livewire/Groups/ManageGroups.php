<?php

namespace App\Livewire\Groups;

use App\Livewire\Forms\GroupForm;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class ManageGroups extends Component
{
    #[Locked]
    public Event $event;

    /** @var Collection<Group> */
    #[Locked]
    public Collection $groups;

    /** @var Collection<Booking> */
    #[Locked]
    public Collection $bookingsWithoutGroup;

    public string $sort = 'name';

    public array $bookingOptionIds;

    public GroupForm $form;

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->loadData();
        $this->bookingOptionIds = $event->bookingOptions->pluck('id')->toArray();
    }

    private function loadData(): void
    {
        $this->event->load([
            'bookingOptions' => fn (HasMany $bookingOptionsQuery) => $bookingOptionsQuery->withCount([
                'bookings',
            ]),
            /** Bookings loaded by @see Event::getBookings() */
            'groups.event',
        ]);

        $this->groups = $this->event->groups->keyBy('id');

        $this->bookingsWithoutGroup = $this->event->getBookings()->filter(
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
        $this->groups = $this->groups->sortBy('name', SORT_STRING | SORT_FLAG_CASE);
    }

    #[On('group-updated')]
    public function updateGroup(Group $group): void
    {
        $this->authorize('update', $group);

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
            $group->bookings()->sync([]); // Required for soft-deleted bookings.
            $this->groups->forget($groupId);
            $group->delete();
            Session::flash('success', __('Group :name deleted successfully.', [
                'name' => $group->name,
            ]));
        }
    }

    private function getGroupById($groupId): ?Group
    {
        return $this->groups[$groupId] ?? null;
    }

    public function moveBooking($bookingId, $groupId): void
    {
        $groupId = (int) $groupId;

        /** @var Booking $booking */
        $booking = Booking::query()->find((int) $bookingId);
        if (isset($booking) && $booking->bookingOption->event->is($this->event)) {
            $this->authorize('manageGroup', $booking);

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

        return view('livewire.groups.manage-groups');
    }

    /**
     * React on changes of {@see self::$sort}
     */
    public function updatedSort(): void
    {
        $this->sortBookings();
    }

    /**
     * React on changes of {@see self::$bookingOptionIds}.
     */
    public function updatedBookingOptionIds(): void
    {
        $this->bookingOptionIds = array_map('intval', $this->bookingOptionIds);
    }

    private function sortBookings(): void
    {
        $this->groups = $this->groups
            ->map(function (Group $group) {
                $group['bookings'] = $this->sortBookingsInGroup(
                    $this->event->getBookings()->filter(
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
        return Booking::sort($bookings, $this->sort);
    }
}
