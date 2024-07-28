<?php

namespace App\Exports;

use App\Exports\Traits\ExportsToExcel;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class GroupsExportSpreadsheet extends Spreadsheet
{
    use ExportsToExcel;

    public function __construct(
        private readonly Event $event,
        private readonly string $sort,
    ) {
        parent::__construct();

        $this->setMetaData($this->event->name);
        $this->event->load([
            'groups.bookings',
        ]);

        $rowCount = $this->event->groups
            ->max(fn (Group $group) => $group->bookings->count());

        $parentEvent = $this->event->parentEvent;

        $bookings = [];
        foreach ($this->event->groups as $group) {
            $bookings[$group->id] = Booking::sort($group->bookings, $this->sort)
                ->map(
                    function (Booking $booking) use ($group, $parentEvent) {
                        $name = $booking->first_name . ' ' . $booking->last_name;

                        if (isset($parentEvent)) {
                            $group = $booking->getGroup($parentEvent);
                            if (isset($group)) {
                                $name .= ' (' . $group->name . ')';
                            }
                        }

                        return $name;
                    }
                )
                ->values()
                ->toArray();
        }

        self::fillSheetFromCollection(
            $this->getActiveSheet(),
            $this->event->name,
            Collection::range(0, $rowCount - 1),
            $this->event->groups
                ->map(fn (Group $group) => $group->name)
                ->toArray(),
            fn (int $row) => $this->event->groups
                ->map(fn (Group $group) => $bookings[$group->id][$row] ?? null)
                ->toArray(),
        );
    }
}
