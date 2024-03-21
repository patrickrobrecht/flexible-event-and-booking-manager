<?php

namespace App\Http\Controllers;

use App\Exports\GroupsExportSpreadsheet;
use App\Http\Controllers\Traits\StreamsExport;
use App\Http\Requests\Filters\GroupFilterRequest;
use App\Http\Requests\GenerateGroupsRequest;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use App\Options\GroupGenerationMethod;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupController extends Controller
{
    use StreamsExport;

    public function index(
        Event $event,
        GroupFilterRequest $request
    ): StreamedResponse|View {
        $this->authorize('viewGroups', $event);

        if ($request->query('output') === 'export') {
            $this->authorize('exportGroups', $event);

            $fileName = $event->slug . '-' . __('Groups');

            return $this->streamExcelExport(
                new GroupsExportSpreadsheet(
                    $event,
                    $request->validated('sort', 'name')
                ),
                str_replace(' ', '-', $fileName) . '.xlsx',
            );
        }

        return view('groups.group_index', [
            'event' => $event
                ->load([
                    'bookingOptions' => fn (HasMany $bookingOptionsQuery) => $bookingOptionsQuery->withCount([
                        'bookings',
                    ]),
                ]),
        ]);
    }

    public function generate(Event $event, GenerateGroupsRequest $request): RedirectResponse
    {
        $this->authorize('create', Group::class);

        $method = GroupGenerationMethod::from($request->validated('method'));
        $groupsCount = (int) $request->validated('groups_count');

        $bookingOptionIds = $request->validated('booking_option_id', []);
        $bookings = $event->getBookings()
            ->filter(fn (Booking $booking) => (
                in_array($booking->bookingOption->id, $bookingOptionIds, true)
            ));

        $generatedGroups = $method->generateGroups($groupsCount, $bookings);
        foreach ($generatedGroups as $groupIndex => $groupMembers) {
            $group = $event->groups()
                ->firstOrCreate([
                    'name' => __('Group') . ' ' . $groupIndex,
                ]);

            /** @var Booking $groupMember */
            foreach ($groupMembers as $groupMember) {
                $currentGroup = $groupMember->getGroup($event);
                if (isset($currentGroup)) {
                    $groupMember->groups()->detach($currentGroup);
                }

                $groupMember->groups()->attach($group);
            }
        }

        return redirect(route('groups.index', $event));
    }
}
