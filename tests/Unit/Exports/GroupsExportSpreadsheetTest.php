<?php

namespace Tests\Unit\Exports;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Exports\GroupsExportSpreadsheet;
use App\Models\Event;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(GroupsExportSpreadsheet::class)]
#[CoversClass(Event::class)]
class GroupsExportSpreadsheetTest extends TestCase
{
    public function testSubHeadlineContainsDateAndPubliclyVisibleResponsibleUsers(): void
    {
        $event = self::createEventWithBookingOptions(Visibility::Private);
        $event->started_at = Carbon::parse('2026-08-01 10:00');
        $event->finished_at = Carbon::parse('2026-08-01 16:00');
        $event->save();

        $user = self::createUser();
        $event->saveResponsibleUsers([
            'responsible_user_id' => [$user->id],
            'responsible_user_data' => [$user->id => ['publicly_visible' => true]],
        ]);

        $sheet = (new GroupsExportSpreadsheet($event, 'name'))->getActiveSheet();

        self::assertSame($event->name, $sheet->getCell('A1')->getValue());
        $subHeadline = $sheet->getCell('A2')->getValue();
        self::assertIsString($subHeadline);
        self::assertStringContainsString(formatDateTime($event->started_at), $subHeadline);
        self::assertStringContainsString(formatTime($event->finished_at), $subHeadline);
        self::assertStringContainsString($user->name, $subHeadline);
    }

    public function testSubHeadlineOmitsResponsibleUsersWhenNoneArePubliclyVisible(): void
    {
        $event = self::createEventWithBookingOptions(Visibility::Private);
        $event->started_at = Carbon::parse('2026-08-01 10:00');
        $event->finished_at = null;
        $event->save();

        $this->actingAsUserWithAbility(Ability::ExportGroupsOfEvent);

        $sheet = (new GroupsExportSpreadsheet($event, 'name'))->getActiveSheet();

        $subHeadline = $sheet->getCell('A2')->getValue();
        self::assertIsString($subHeadline);
        self::assertSame(
            __('starting :start', [
                'start' => formatDateTime($event->started_at),
            ]),
            $subHeadline
        );
    }
}
