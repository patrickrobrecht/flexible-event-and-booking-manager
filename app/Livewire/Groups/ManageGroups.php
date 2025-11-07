<?php

namespace App\Livewire\Groups;

use App\Livewire\Forms\GroupForm;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use App\Traits\LoadsPropertiesFromSession;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;
use Portavice\Bladestrap\Support\Options;

class ManageGroups extends Component
{
    use LoadsPropertiesFromSession;

    /**
     * @var array<string, string>
     */
    private array $propertiesSavedInSession = [
        'sort' => 'string',
        'bookingOptionIds' => 'int[]',
        'showBookingData' => 'string[]',
        'showFields' => 'int[]',
    ];

    #[Locked]
    public Event $event;

    /** @var Collection<int, Group> */
    #[Locked]
    public Collection $groups;

    /** @var Collection<int, Booking> */
    #[Locked]
    public Collection $bookingsWithoutGroup;

    public string $sort = 'name';

    /** @var int[] */
    public array $bookingOptionIds;

    /** @var string[] */
    public array $showBookingData = [
        'booked_at',
    ];

    /** @var int[] */
    public array $showFields = [];

    public GroupForm $form;

    public function mount(Event $event): void
    {
        $this->event = $event;
        $this->loadData();
        /** @phpstan-ignore-next-line assign.propertyType */
        $this->bookingOptionIds = $this->event->getBookingOptions()->pluck('id')->toArray();

        $this->loadSettingsFromSession();
    }

    private function loadData(): void
    {
        $this->event->loadMissing([
            /** Bookings loaded by @see Event::getBookingOptions() */
            /** Bookings loaded by @see Event::getBookings() */
            'bookingOptions.formFields',
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

        $validated = $this->form->validateGroupForEvent($this->event);

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

    public function deleteGroup(int $groupId): void
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

    private function getGroupById(int $groupId): ?Group
    {
        return $this->groups[$groupId] ?? null;
    }

    public function moveBooking(int $bookingId, int $groupId): void
    {
        /** @var ?Booking $booking */
        $booking = Booking::query()->find($bookingId);
        if (isset($booking) && $booking->bookingOption->event->is($this->event->parentEvent ?? $this->event)) {
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

        return view('livewire.groups.manage-groups', [
            'displayOptions' => $this->getDisplayOptions(),
        ]);
    }

    private function getDisplayOptions(): Options
    {
        $conditionalDisplayOptions = [];
        if (Auth::user()?->can('viewAnyPaymentStatus', Booking::class)) {
            $conditionalDisplayOptions['paid_at'] = __('Payment status');
        }
        if (Auth::user()?->can('updateAnyBookingComment', Booking::class)) {
            $conditionalDisplayOptions['comment'] = __('Comments');
        }

        return Options::fromArray([
            'booked_at' => __('Booking date'),
            ...$conditionalDisplayOptions,
            'email' => __('E-mail'),
            'phone' => __('Phone number'),
            'address' => __('Address'),
        ]);
    }

    /**
     * React on changes of {@see self::$sort}.
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

    /**
     * React on any changes.
     */
    public function updated(): void
    {
        $this->storeSettingsInSession();
    }

    private function sortBookings(): void
    {
        $bookings = $this->event->getBookings();
        if (count($this->showFields) > 0) {
            /** @phpstan-ignore-next-line argument.type */
            $bookings->load([
                'formFieldValues' => fn (HasMany $formFieldValues) => $formFieldValues
                    ->whereIn('form_field_id', $this->showFields),
            ]);
        }
        $this->groups = $this->groups
            ->map(function (Group $group) use ($bookings) {
                $group['bookings'] = $this->sortBookingsInGroup(
                    /** @phpstan-ignore-next-line argument.type */
                    $bookings->filter(fn (Booking $booking) => $booking->getGroup($this->event)?->is($group))
                );

                return $group;
            });

        $this->bookingsWithoutGroup = $this->sortBookingsInGroup($this->bookingsWithoutGroup);
    }

    /**
     * @param Collection<int, Booking> $bookings
     *
     * @return Collection<int, Booking>
     */
    private function sortBookingsInGroup(Collection $bookings): Collection
    {
        return Booking::sort($bookings, $this->sort);
    }

    public function getSessionKey(string $propertyName): string
    {
        return 'groups-settings-' . $this->event->id . '-' . $propertyName;
    }
}
