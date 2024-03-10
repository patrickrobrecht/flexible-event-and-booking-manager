<?php

namespace App\Http\Controllers;

use App\Exports\GroupsExportSpreadsheet;
use App\Http\Controllers\Traits\StreamsExport;
use App\Http\Requests\Filters\GroupFilterRequest;
use App\Models\Event;
use App\Models\Group;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GroupController extends Controller
{
    use StreamsExport;

    public function index(
        Event $event,
        GroupFilterRequest $request
    ): StreamedResponse|View {
        $this->authorize('viewAny', [Group::class, $event]);

        if ($request->query('output') === 'export') {
            $this->authorize('exportAny', [Group::class, $event]);

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
            'event' => $event,
        ]);
    }
}
